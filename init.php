<?php
require __DIR__ . '/vendor/autoload.php';

use Sandbox\LoggerSingleton;

define('APPLICATION_DIR', __DIR__);

$logger = LoggerSingleton::getInstance();
