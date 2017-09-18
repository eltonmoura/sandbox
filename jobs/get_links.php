#!/usr/bin/php
<?php
require_once __DIR__ . "/../init.php";

if (!isset($argv[1])) {
    die("Uso: " . $argv[0] . " url|file\n");
}

$content = file_get_contents($argv[1]);

$linkPattern = "#<td><a\shref=\"(.*?)\"#is";

$links = [];
if (preg_match_all($linkPattern, $content, $matches)) {
    $links = $matches[1];
    sort($links);
}

file_put_contents($argv[2], implode(PHP_EOL, $links));

die("Done\n");
