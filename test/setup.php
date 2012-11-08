<?php

$fscLibPath = realpath(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'library', 'FileServerClient')));
require_once $fscLibPath . '/Adapter/AdapterInterface.php';
require_once $fscLibPath . '/FileServerClient.php';
require_once $fscLibPath . '/Exception/FileExistsException.php';
require_once $fscLibPath . '/Exception/FileNotExistsException.php';
require_once $fscLibPath . '/Adapter/FileSystem.php';