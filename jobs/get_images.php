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
    '#\'//tn.hitomi.la/smalltn/(.*?).jpg\'#is',
    'https://aa.hitomi.la/galleries/<replace>'
);
$comicDownloader->setTitleRegex('#<h1><a\shref="/reader/.*?>(.*?)</a></h1>#is');
$comicDownloader->setAuthorRegex('#<li><a\shref="/artist/.*?>(.*?)</a></li>#is');

$comicDownloader->setDataPath('/home/eltonmoura/crypt/comics/');
$comicDownloader->run();

$logger->info('Done.');
