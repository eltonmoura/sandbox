<?php
namespace Sandbox;

use \DirectoryIterator;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;

class DirHandler extends DirectoryIterator
{
    public function removeRecursively()
    {
        $path = $this->getPath();

        $fileSPLObjects = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($fileSPLObjects as $fileSPLObject) {
            if ($fileSPLObject->isDir()) {
                if (in_array($fileSPLObject->getFilename(), ['.','..'])) {
                    continue;
                }
                rmdir($fileSPLObject->getPathname());
            } elseif ($fileSPLObject->isFile() || $fileSPLObject->isLink()) {
                unlink($fileSPLObject->getPathname());
            }
        }
        rmdir($path);
        return true;
    }
}
