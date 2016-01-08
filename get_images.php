<?php
$data_path = "/usr/local/data/comics";
$link_pattern = "#<div\sclass=\"img\-url\">//(.*?)</div>#is";
$base_url = "https://";

if (!isset($argv[1])) {
    die("Uso: " . $argv[0] . " url|file\n");
}

$content = file_get_contents($argv[1]);

if (!preg_match_all($link_pattern, $content, $matches)) {
    die("Não foram encontradas imagens\n");
}

print("Foram encontradas " . count($matches[1]) . " imagens\n");

if (preg_match("#<title>(.*?)\|.*?</title>#is", $content, $m)) {
    $dest_dir = $data_path . "/" . asSlug($m[1]);
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
    copy($url, $dest_file);
}

makeComicBookFromDir($dest_dir);

print("Done.\n");

function asSlug($text)
{
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}

function makeComicBookFromDir($dir)
{
    if (!is_dir($dir)) {
        return false;
    }
    $zipFile = $dir . ".cbz";
    $zipArchive = new ZipArchive();
    if (!$zipArchive->open($zipFile, ZIPARCHIVE::OVERWRITE)) {
        die("Failed to create archive\n");
    }
    $zipArchive->addGlob($dir . "/*");
    if (!$zipArchive->status == ZIPARCHIVE::ER_OK) {
        echo "Failed to write files to zip\n";
    }
    $zipArchive->close();
    return true;
}
