file-server-client
==================

Single simple facade for different types of file servers.
The library represents interface to perform basic file operations: CRUD, rename, copy, aviability check.

Single interface
----------------
```php
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
```

Adapters
--------

Every concrete file server must have it's own adapter. Adapter implements all interface methods.

Currently two adapters are bundled with source code: FileSystem and HttpFileServer.

Usage examples
--------------

Usage example can be found in test files: https://github.com/feedbee/file-server-client/tree/master/test/

License
--------

BSD License: http://www.opensource.org/licenses/bsd-license.php  

Copyright (c) 2012, Leontyev Valera <feedbee at gmail dot com>.