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

namespace FileServerClient;

class FileServerClient implements Adapter\AdapterInterface {
	private $adapter;

	public function __construct(array $options = array()) {
		if (isset($options['adapter']))
		{
			$adapterOptions = array();
			if (isset($options['adapter_options']))
			{
				$adapterOptions = $options['adapter_options'];
			}

			$adapter = $options['adapter'];
			if (is_object($adapter)) {
				$this->adapter = $adapter;
			}
			else if (is_string($adapter) && class_exists($adapter)) {
				$this->adapter = new $adapter($adapterOptions);
			}
			else if (is_string($adapter) && class_exists($className = 'FileServerClient\\Adapter\\' . ucfirst($adapter))) {
				$this->adapter = new $className($adapterOptions);
			}
			else {
				throw new \Exception(__METHOD__ . ': can\'t understand `adapter` option');
			}
		} else {
			throw new \Exception(__METHOD__ . ': `adapter` option is not set while required');
		}
	}

	public function has($targetName) {
		return $this->adapter->has($targetName);
	}

	public function get($targetName) {
		return $this->adapter->get($targetName);
	}

	public function getStream($targetName) {
		return $this->adapter->getStream($targetName);
	}

	public function put($sourceName, $targetName, $override = false) {
		return $this->adapter->put($sourceName, $targetName, $override);
	}

	public function putStream($sourceStream, $targetName, $override = false) {
		return $this->adapter->putStream($sourceStream, $targetName, $override);
	}

	public function delete($targetName) {
		return $this->adapter->delete($targetName);
	}

	public function rename($fromName, $toName) {
		return $this->adapter->rename($fromName, $toName);
	}

	public function copy($sourceName, $targetName) {
		return $this->adapter->copy($sourceName, $targetName);
	}
}