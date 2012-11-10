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
	FileServerClient\Exception\WrongTargetNameException;

class FileSystem extends AbstractAdapter {
	private $basePath;

	public function __construct(array $options = array()) {
		if (isset($options['base_path']))
		{
			$this->basePath = $options['base_path'];
		}
	}

	private function prepareName($name) {
		if (false !== ($pos = strpos($name, "\n"))) {
			$name = substr($name, 0, $pos);
		}

		if (DIRECTORY_SEPARATOR != '/') {
			$name = str_replace('/', DIRECTORY_SEPARATOR, $name);
		}

		return $name;
	}

	public function has($targetName) {
		$targetName = $this->prepareName($targetName);

		return file_exists($this->basePath . $targetName);
	}

	public function get($targetName) {
		$targetName = $this->prepareName($targetName);

		if (!$this->has($targetName)) {
			throw new FileNotExistsException($targetName);
		}

		return file_get_contents($this->basePath . $targetName);
	}

	public function getStream($targetName) {
		$targetName = $this->prepareName($targetName);

		if (!$this->has($targetName)) {
			throw new FileNotExistsException($targetName);
		}

		$stream = fopen($this->basePath . $targetName, 'r');
		if (!$stream) {
			throw new \RuntimeException(__METHOD__ . ": can't open stream to file `$targetName`");
		}

		return $stream;
	}

	public function put($source, $targetName, $override = false) {
		$targetName = $this->prepareName($targetName);

		if (!$override && $this->has($targetName)) {
			throw new FileExistsException($targetName);
		}

		$this->createSubdirectories($targetName);

		file_put_contents($this->basePath . $targetName, $source);

		return $targetName;
	}

	public function putStream($sourceStream, $targetName, $override = false) {
		$targetName = $this->prepareName($targetName);

		if (!$override && $this->has($targetName)) {
			throw new FileExistsException($targetName);
		}

		$this->createSubdirectories($targetName);

		$toStream = fopen($this->basePath . $targetName, 'w');
		if (!$toStream) {
			throw new \RuntimeException(__METHOD__ . "can't open target file `{$this->basePath}{$targetName}`");
		}
		stream_copy_to_stream($sourceStream, $toStream);
		fclose($toStream);

		return $targetName;
	}

	private function createSubdirectories($targetName) {
		$targetName = $this->prepareName($targetName);

		$path = $this->basePath;
		$dirs = array_filter(explode(DIRECTORY_SEPARATOR, $targetName), function ($el) { return $el !== ''; });
		array_pop($dirs); // remove filename at the last position.
		foreach ($dirs as $dir) {
			$path .= DIRECTORY_SEPARATOR . $dir;
			if (!is_dir($path)) {
				if (file_exists($path)) {
					throw new WrongTargetNameException("can't store file at `$targetName` - file with a PARTICULAR same name already existed");
				}
				$r = mkdir($path);
				if (!$r) {
					throw new \RuntimeException(__METHOD__ . "can't create directory `$path`");
				}
			}
		}
	}

	public function delete($targetName) {
		$targetName = $this->prepareName($targetName);

		if (file_exists($this->basePath . $targetName)) {
			unlink($this->basePath . $targetName);

			$this->removeSubdirectories($targetName);
		}

		return $targetName;
	}

	private function removeSubdirectories($targetName) {
		$path = array_filter(explode(DIRECTORY_SEPARATOR, $targetName), function ($el) { return $el !== ''; });
		array_pop($path); // remove filename at the last position

		while (count($path) > 0) {
			$relDirectory = implode(DIRECTORY_SEPARATOR, $path);

			$curDir = $this->basePath . DIRECTORY_SEPARATOR . $relDirectory;

			if (is_dir($curDir) && count(scandir($curDir)) < 3) { // empty directory contains 2 files: . and ..
				rmdir($curDir);
			}

			array_pop($path);
		}
	}

	public function rename($fromName, $toName) {
		$fromName = $this->prepareName($fromName);
		$toName = $this->prepareName($toName);

		$this->createSubdirectories($toName);
		rename($this->basePath . $fromName, $this->basePath . $toName);
		$this->removeSubdirectories($fromName);
	}

	public function copy($sourceName, $targetName) {
		$targetName = $this->prepareName($targetName);
		$sourceName = $this->prepareName($sourceName);

		$this->createSubdirectories($targetName);
		copy($this->basePath . $sourceName, $this->basePath . $targetName);
	}
}