<?php

// put & get
$client->put('Test', '/a');
print "Put & Get: " . ($client->get('/a') == 'Test' ? 'Ok' : 'Fail') . PHP_EOL;

// override
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
print "Put (override): Ok";
print "Put (override): " . ($client->get('/a') == 'Test2' ? 'Ok' : 'Fail') . PHP_EOL;

// getStream
$stream = $client->getStream('/a');
print "GetStream: " . (stream_get_contents($stream) == 'Test2' ? 'Ok' : 'Fail') . PHP_EOL;
fclose($stream);

// rename
$client->rename('/a', '/b/a');
print "Rename: " . ($client->get('/b/a') == 'Test2' && $client->has('/a') == false ? 'Ok' : 'Fail') . PHP_EOL;

// copy
$client->copy('/b/a', '/b/b');
print "Copy: " . ($client->get('/b/b') == 'Test2' && $client->has('/b/a') == true ? 'Ok' : 'Fail') . PHP_EOL;

// delete
$client->delete('/b/a');
$client->delete('/b/b');
print "Delete: " . ($client->delete('/b/a') && $client->delete('/b/b') ? 'Ok' : 'Fail') . PHP_EOL;

// put stream
$temp = tmpfile();
fwrite($temp, "Test3");
fseek($temp, 0);
$client->putStream($temp, '/stream');
print "PutStream: " . ($client->get('/stream') == 'Test3' ? 'Ok' : 'Fail') . PHP_EOL;

try {
	$client->put('Test4', '/stream');
	print "FileExistsException (Stream): Fail" . PHP_EOL;
}
catch (FileServerClient\Exception\FileExistsException $e)
{
	print "FileExistsException (Stream): Ok" . PHP_EOL;
}
$client->put('Test4', '/stream', true);
print "PutStream (override): " . ($client->get('/stream') == 'Test4' ? 'Ok' : 'Fail') . PHP_EOL;

$client->delete('/stream');
fclose($temp); // this removes the file