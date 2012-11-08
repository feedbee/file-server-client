file-server-client
==================

Single simple facade for different types of file servers.

Single interface
===
```php
interface AdapterInterface {
  public function __construct(array $options = array());

	public function has($targetName);

	public function get($targetName);

	public function getStream($targetName);

	public function put($source, $targetName, $override = false);

	public function putStream($sourceStream, $targetName, $override = false);

	public function delete($targetName);

	public function rename($fromName, $toName);

	public function copy($sourceName, $targetName);
}
```

Adapters
========

Every concreate file server must have it's own adapter. Adapter implements all interface methods.

Usage examples
==============

Usage example can be found in test files: https://github.com/feedbee/file-server-client/tree/master/test/
