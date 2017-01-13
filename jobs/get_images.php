#!/usr/bin/php
<?php
require_once __DIR__ . "/../init.php";

use Sandbox\ComicDownloader;

#parse_str(implode('&', array_slice($argv, 1)), $_GET);
#http://php.net/manual/pt_BR/function.getopt.php

if (!isset($argv[1])) {
    die("Uso: " . $argv[0] . " url|file\n");
}

$galeryUrl = $argv[1];

$logger->info(sprintf('Downloading %s', $galeryUrl));

#try {
    $comicDownloader = new ComicDownloader($galeryUrl);
    $comicDownloader->run();
#} catch (Exception $e) {
#    print($e->getMessage()."\n");
#}

$logger->info('Done.');
