<?php

// put
$client->put('Test', '/a');

// override
print "Exists: " . ($client->has('/a') ? 'yes' : 'no') . PHP_EOL;
try {
	$client->put('Test2', '/a');
}
catch (FileServerClient\Exception\FileExistsException $e)
{
	print "FileExistsException: Ok" . PHP_EOL;
}
$client->put('Test2', '/a', true);

// get
print "Get: " . ($client->get('/a') == 'Test2' ? 'Ok' : 'Fail') . PHP_EOL;

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