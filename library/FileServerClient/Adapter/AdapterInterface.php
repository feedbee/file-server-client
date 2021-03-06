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

interface AdapterInterface {
	public function __construct(array $options = array());

	public function has($targetName);

	public function get($targetName);

	public function getFile($targetName, $fileName = null);

	public function getStream($targetName);

	public function put($source, $targetName, $override = false);

	public function putFile($fileName, $targetName, $override = false);

	public function putStream($sourceStream, $targetName, $override = false);

	public function delete($targetName);

	public function rename($fromName, $toName);

	public function copy($sourceName, $targetName);
}