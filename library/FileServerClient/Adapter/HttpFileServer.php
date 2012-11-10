<?php
/**
 * File Server Client
 *
 * Copyright (c) 2012, Leontyev Valera <feedbee at gmail dot com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Valera Leontyev nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright  2012 Leontyev Valera <feedbee at gmail dot com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    $Id: 1.0$
 * @author     Leontyev Valera <feedbee at gmail dot com>
 */

namespace FileServerClient\Adapter;

use FileServerClient\Exception\FileExistsException,
	FileServerClient\Exception\FileNotExistsException,
	FileServerClient\Exception\NotImplementedException;

/**
 * Client adapter for Http File Server https://github.com/feedbee/http-file-server
 */
class HttpFileServer extends AbstractAdapter {
	private $baseUri;
	private $requestsTimeout = 1; // seconds

	public function __construct(array $options = array()) {
		if (isset($options['base_uri']))
		{
			$this->baseUri = $options['base_uri'];
		}
		else
		{
			throw new \Exception('Config option `base_uri` is required');
		}
		if (isset($options['requests_timeout']))
		{
			$this->requestsTimeout = $options['requests_timeout'];
		}
	}

	private function prepareName($name) {
		if (false !== ($pos = strpos($name, "\n"))) {
			$name = substr($name, 0, $pos);
		}

		return $name;
	}

	public function has($targetName) {
		// throw new NotImplementedException('Method `has` is not implemented in Http File Server');
		$result = $this->sendRequest('HEAD', $targetName);

		return $result['code'] / 100 == 2;
	}

	public function get($targetName) {
		$targetName = $this->prepareName($targetName);

		$result = $this->sendRequest('GET', $targetName);

		if ($result['code'] / 100 != 2) {
			if ($result['code'] == 404) {
				throw new FileNotExistsException($targetName);
			}
			throw new \RuntimeException(__METHOD__ . ": can't read file `$targetName`, code {$result['code']}");
		}

		return $result['body'];
	}

	public function getStream($targetName) {
		$targetName = $this->prepareName($targetName);

		if (!$this->has($targetName)) {
			throw new FileNotExistsException($targetName);
		}

		$stream = fopen($this->baseUri . $targetName, 'r');
		if (!$stream) {
			throw new \RuntimeException(__METHOD__ . ": can't open stream to file `{$this->baseUri}$targetName`");
		}
		return $stream;
	}

	public function put($source, $targetName, $override = false) {
		$targetName = $this->prepareName($targetName);

		if (!$override && $this->has($targetName)) {
			throw new FileExistsException($targetName);
		}

		$tempFileName = tempnam(sys_get_temp_dir(), 'fsc');
		$result = file_put_contents($tempFileName, $source);
		if (false === $result) {
			throw new \RuntimeException(__METHOD__ . ": can't write to temporary file `$tempFileName`");
		}

		$this->sendRequest('PUT', $targetName, $tempFileName);

		unlink($tempFileName);

		return $targetName;
	}

	public function putStream($sourceStream, $targetName, $override = false) {
		$targetName = $this->prepareName($targetName);

		if (!$override && $this->has($targetName)) {
			throw new FileExistsException($targetName);
		}

 		$tempFileName = tempnam(sys_get_temp_dir(), 'fsc');
		$temp = fopen($tempFileName, 'w');
		if (!$temp) {
			throw new \RuntimeException(__METHOD__ . ": can't open temporary file");
		}
		$result = stream_copy_to_stream($sourceStream, $temp);
		if (false === $result) {
			throw new \RuntimeException(__METHOD__ . ": can't copy stream to temporary file");
		}
		fclose($temp);

		$this->sendRequest('PUT', $targetName, $tempFileName);

		unlink($tempFileName);

		return $targetName;
	}

	public function delete($targetName) {
		$targetName = $this->prepareName($targetName);

		$result = $this->sendRequest('DELETE', $targetName);
		if ($result['code'] / 100 != 2) {
			throw new \RuntimeException(__METHOD__ . ": can't delete file `$targetName`, code {$result['code']}");
		}

		return $result['body'];
	}

	public function rename($fromName, $toName) {
		throw new NotImplementedException;
	}

	public function copy($sourceName, $targetName) {
		throw new NotImplementedException;
	}

	private function sendRequest($method, $targetName, $sendFile = null) {
		$method = strtoupper($method);

		$ch = curl_init();
		$encodedTargetName = urlencode($targetName);
		$url = "{$this->baseUri}?url=$encodedTargetName";
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->requestsTimeout);

		switch ($method) {
			case 'HEAD': curl_setopt($ch, CURLOPT_NOBODY, true); break;
			case 'PUT': curl_setopt($ch, CURLOPT_PUT, true); break;
			case 'POST': curl_setopt($ch, CURLOPT_POST, true); break;
			case 'DELETE': curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE'); break;
		}

		$fileHandler = null;
		if (!is_null($sendFile)) {
			$fileSize = filesize($sendFile);
			$fileHandler = fopen($sendFile, 'r');
			if (!$fileHandler) {
				throw new \RuntimeException(__METHOD__ . ": can't open file `$sendFile` for uploading");
			}

			curl_setopt($ch, CURLOPT_INFILE, $fileHandler);
			curl_setopt($ch, CURLOPT_INFILESIZE, $fileSize);
			curl_setopt($ch, CURLOPT_READFUNCTION, function ($ch, $fileHandler, $blockLength) {
				return fread($fileHandler, $blockLength);
			});
		}

		curl_setopt($ch, CURLOPT_URL, $url);

		// debug
		// curl_setopt($ch, CURLOPT_VERBOSE, TRUE);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if (!is_null($sendFile)) {
			fclose($fileHandler);
		}

		if (false === $response) {
			$error = curl_error($ch);
			curl_close($ch);
			throw new \RuntimeException(__METHOD__ . ": curl request failed with error: `$error`");
		}
		curl_close($ch);
		
		return array('code' => $httpCode, 'body' => $response);
	}
}