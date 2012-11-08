<?php

require __DIR__ . '/setup.php';

$client = new \FileServerClient\FileServerClient(array(
	'adapter' => 'FileSystem',
	'adapter_options' => array(
		'base_path' => __DIR__ . '/storage'
	),
));

require __DIR__ . '/std-test.php';