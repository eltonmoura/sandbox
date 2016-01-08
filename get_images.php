<?php
$data_path = "/usr/local/data/comics";
$link_pattern = "#<div\sclass=\"img\-url\">//(.*?)</div>#is";
$base_url = "https://";

if (!isset($argv[1])) { //|| !is_file($argv[1]
    die("Uso: " . $argv[0] . " url|file\n");
}

$content = file_get_contents($argv[1]);

if (preg_match_all($link_pattern, $content, $matches)) {
    #print_r($matches[1]);
    foreach ($matches[1] as $link) {
        if (!preg_match("#/([-_0-9a-zA-Z]+)/([-_0-9a-zA-Z]+\.jpg)#", $link, $m)) {
            die("Não foi possível gerar o nome do arquivo de destino em '$link'\n");
        }

        $gallery = $m[1];
        $dest_dir = $data_path . "/" . $gallery;
        if (!is_dir($dest_dir)) {
            mkdir($dest_dir);
        }

        $dest_file = $dest_dir . "/" . $m[2];

        if (is_file($dest_file)) {
            continue;
        }

        $url = $base_url . $link;
        print("Copiando $url para $dest_file.\n");
        copy($url, $dest_file);
    }

    $zipFile = $data_path . "/" . $gallery . ".cbz";
    $zipArchive = new ZipArchive();
    if (!$zipArchive->open($zipFile, ZIPARCHIVE::OVERWRITE)) {
        die("Failed to create archive\n");
    }
    $zipArchive->addGlob($dest_dir . "/*.jpg");
    if (!$zipArchive->status == ZIPARCHIVE::ER_OK) {
        echo "Failed to write files to zip\n";
    }
    $zipArchive->close();

}
print("Done.\n");
