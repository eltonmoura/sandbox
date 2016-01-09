<?php
class Util
{
    public static function asSlug($text)
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

    public static function makeComicBookFromDir($dir)
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
}
