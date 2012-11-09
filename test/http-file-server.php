<?php

require __DIR__ . '/setup.php';

$client = new \FileServerClient\FileServerClient(array(
	'adapter' => 'HttpFileServer',
	'adapter_options' => array(
		'base_uri' => 'http://localhost/praca-dev/http-file-server/index.php',
		'requests_timeout' => 5
	),
));

require __DIR__ . '/std-test.php';