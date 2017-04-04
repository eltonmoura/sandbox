#!/usr/bin/php
<?php
require_once __DIR__ . "/../init.php";

use Sandbox\DirHandler;

$path = '/home/eltonms/testedir';
if (is_dir($path)) {
    $dirHandler = new DirHandler($path);
    $dirHandler->removeRecursively();
}
