<?php
include("./lib/Util.class.php");
include("./lib/SimpleHttpClient.class.php");

$data_path = "/usr/local/data/comics";
$link_pattern = "#<div\sclass=\"img\-url\">//(.*?)</div>#is";
$base_url = "https://";

if (!isset($argv[1])) {
    die("Uso: " . $argv[0] . " url|file\n");
}

$client = new SimpleHttpClient();
$content = $client->get($argv[1]);

if (!preg_match_all($link_pattern, $content, $matches)) {
    die("Não foram encontradas imagens\n$content\n");
}

print("Foram encontradas " . count($matches[1]) . " imagens\n");

if (preg_match("#<title>(.*?)\|.*?</title>#is", $content, $m)) {
    $dest_dir = $data_path . "/" . Util::asSlug($m[1]);
    if (!is_dir($dest_dir)) {
        mkdir($dest_dir);
    }
}

$i = 0;
foreach ($matches[1] as $link) {
    $i++;
    if (!preg_match("#/([-_0-9a-zA-Z]+)/([-\._0-9a-zA-Z]+\.\w{3})#", $link, $m)) {
        die("Não foi possível gerar o nome do arquivo de destino em '$link'\n");
    }

    $dest_file = $dest_dir . "/" . $m[2];
    if (is_file($dest_file)) {
        continue;
    }

    $url = $base_url . $link;
    print("Copiando $url (" . $i .")\n");
    
    $client->setBinaryTransfer(true);
    $content = $client->get($url);

    file_put_contents($dest_file, $content);
}
$client->close();

Util::makeComicBookFromDir($dest_dir);

print("Done.\n");
