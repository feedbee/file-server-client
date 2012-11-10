<?php

// has
try {
	print "Has: " . ($client->has('/a') === false ? 'Ok' : 'Fail') . PHP_EOL;
} catch (FileServerClient\Exception\NotImplementedException $e) {
	print "Has: not implemented" . PHP_EOL;
}

// put & get
try {
	$client->put('Test', '/a');
	print "Put & Get: " . ($client->get('/a') == 'Test' ? 'Ok' : 'Fail') . PHP_EOL;
} catch (FileServerClient\Exception\NotImplementedException $e) {
	print "Put: not implemented" . PHP_EOL;
}

// put override
try {
	print "Exists: " . ($client->has('/a') ? 'yes' : 'no') . PHP_EOL;
	try {
		$client->put('Test2', '/a');
		print "FileExistsException: Fail" . PHP_EOL;
	}
	catch (FileServerClient\Exception\FileExistsException $e)
	{
		print "FileExistsException: Ok" . PHP_EOL;
	}
	$client->put('Test2', '/a', true);
	print "Put (override): " . ($client->get('/a') == 'Test2' ? 'Ok' : 'Fail') . PHP_EOL;
} catch (FileServerClient\Exception\NotImplementedException $e) {
	print "Put: not implemented" . PHP_EOL;
}

// getStream
try {
	$stream = $client->getStream('/a');
	print "GetStream: " . (stream_get_contents($stream) == 'Test2' ? 'Ok' : 'Fail') . PHP_EOL;
	fclose($stream);
} catch (FileServerClient\Exception\NotImplementedException $e) {
	print "GetStream: not implemented" . PHP_EOL;
}

// getFile
try {
	$fileName = $client->getFile('/a');
	print "GetFile: " . (file_get_contents($fileName) == 'Test2' ? 'Ok' : 'Fail') . PHP_EOL;
	unlink($fileName);
} catch (FileServerClient\Exception\NotImplementedException $e) {
	print "GetFile: not implemented" . PHP_EOL;
}

// rename
try {
	$client->rename('/a', '/b/a');
	print "Rename: " . ($client->get('/b/a') == 'Test2' && $client->has('/a') == false ? 'Ok' : 'Fail') . PHP_EOL;
} catch (FileServerClient\Exception\NotImplementedException $e) {
	print "Rename: not implemented" . PHP_EOL;
	try { $client->delete('/a'); } catch (FileServerClient\Exception\NotImplementedException $e) {}
}

// copy
try {
	$client->copy('/b/a', '/b/b');
	print "Copy: " . ($client->get('/b/b') == 'Test2' && $client->has('/b/a') == true ? 'Ok' : 'Fail') . PHP_EOL;
} catch (FileServerClient\Exception\NotImplementedException $e) {
	print "Copy: not implemented" . PHP_EOL;
}

// delete
try {
	print "Delete: " . ($client->delete('/b/a') && $client->delete('/b/b') ? 'Ok' : 'Fail') . PHP_EOL;
} catch (FileServerClient\Exception\NotImplementedException $e) {
	print "Delete: not implemented" . PHP_EOL;
}

// put stream
$temp = tmpfile();
fwrite($temp, "Test3");
fseek($temp, 0);

try {
	$client->putStream($temp, '/stream');
	print "PutStream: " . ($client->get('/stream') == 'Test3' ? 'Ok' : 'Fail') . PHP_EOL;
} catch (FileServerClient\Exception\NotImplementedException $e) {
	print "PutStream: not implemented" . PHP_EOL;
}

fclose($temp);

// put stream (override)
$temp = tmpfile();
fwrite($temp, "Test4");
fseek($temp, 0);

try {
	try {
		$client->putStream($temp, '/stream');
		print "FileExistsException (Stream): Fail" . PHP_EOL;
	}
	catch (FileServerClient\Exception\FileExistsException $e)
	{
		print "FileExistsException (Stream): Ok" . PHP_EOL;
	}
	$client->putStream($temp, '/stream', true);
	print "PutStream (override): " . ($client->get('/stream') == 'Test4' ? 'Ok' : 'Fail') . PHP_EOL;
} catch (FileServerClient\Exception\NotImplementedException $e) {
	print "PutStream: not implemented" . PHP_EOL;
}

try {
	$client->delete('/stream');
} catch (FileServerClient\Exception\NotImplementedException $e) {}
fclose($temp); // this removes the file

// put file
$fileName = tempnam(sys_get_temp_dir(), 'fsc');
file_put_contents($fileName, 'Test5');

try {
	$client->putFile($fileName, '/put-file');
	print "PutFile: " . ($client->get('/put-file') == 'Test5' ? 'Ok' : 'Fail') . PHP_EOL;
} catch (FileServerClient\Exception\NotImplementedException $e) {
	print "PutFile: not implemented" . PHP_EOL;
}

// put file (override)
file_put_contents($fileName, 'Test6');
try {
	try {
		$client->putFile($fileName, '/put-file');
		print "FileExistsException (File): Fail" . PHP_EOL;
	}
	catch (FileServerClient\Exception\FileExistsException $e)
	{
		print "FileExistsException (File): Ok" . PHP_EOL;
	}
	$client->putFile($fileName, '/put-file', true);
	print "PutFile (override): " . ($client->get('/put-file') == 'Test6' ? 'Ok' : 'Fail') . PHP_EOL;
} catch (FileServerClient\Exception\NotImplementedException $e) {
	print "PutFile: not implemented" . PHP_EOL;
}

try {
	$client->delete('/put-file');
} catch (FileServerClient\Exception\NotImplementedException $e) {}
unlink($fileName);