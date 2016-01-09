<?php
include("./lib/Util.class.php");
include("./lib/SimpleHttpClient.class.php");

$dataPath = "/usr/local/data/comics";
$linkPattern = "#<div\sclass=\"img\-url\">//(.*?)</div>#is";
$imagePattern = "#/([-_0-9a-zA-Z]+)/([-\._0-9a-zA-Z]+\.\w{3})#is";
$titlePattern = "#<title>(.*?)\|[^\|]*?</title>#is";
$baseUrl = "https://";

if (!isset($argv[1])) {
    die("Uso: " . $argv[0] . " url|file\n");
}
$galeryUrl = $argv[1];
$galeryUrl = str_replace("/galleries/", "/reader/", $galeryUrl);

$client = new SimpleHttpClient();
$content = $client->get($galeryUrl);

if (!preg_match_all($linkPattern, $content, $matches)) {
    die("Não foram encontradas imagens\n$content\n");
}

print("Foram encontradas " . count($matches[1]) . " imagens\n");

if (preg_match($titlePattern, $content, $m)) {
    $destDir = $dataPath . "/" . Util::asSlug($m[1]);
    if (!is_dir($destDir)) {
        mkdir($destDir);
    }
}

$client->setBinaryTransfer(true);

$i = 0;
foreach ($matches[1] as $link) {
    $i++;
    if (!preg_match($imagePattern, $link, $m)) {
        die("Não foi possível gerar o nome do arquivo de destino em '$link'\n");
    }

    $destFile = $destDir . "/" . $m[2];
    if (is_file($destFile)) {
        continue;
    }

    $url = $baseUrl . $link;

    print("Copiando $url (" . $i .")\n");
    $content = $client->get($url, $galeryUrl);
    $httpInfo = $client->getHttpInfo();

    if ($httpInfo["http_code"] != "200") {
        print("HttpInfo:\n");
        print_r($httpInfo);
    }

    file_put_contents($destFile, $content);
}
$client->close();

Util::makeComicBookFromDir($destDir, "cbr");

print("Done.\n");
