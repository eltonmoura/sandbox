#!/usr/bin/php
<?php
require_once __DIR__ . "/../init.php";

use Sandbox\ComicDownloader;

if (!isset($argv[1])) {
    die("Uso: " . $argv[0] . " url|file\n");
}

$galeryUrl = $argv[1];

$logger->info(sprintf('Downloading %s', $galeryUrl));

$comicDownloader = new ComicDownloader($galeryUrl);

$comicDownloader->setLinkPregReplace(
    '#src=\"//tn.nozomi.la/(.*?)\.jpg\"#is',
    'https://i.nozomi.la/<replace>'
);

$comicDownloader->setDataPath('/home/eltonmoura/crypt/comics/');

$comicDownloader->run();

$logger->info('Done.');
