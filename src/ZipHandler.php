<?php
namespace Sandbox;

use \ZipArchive;

class ZipHandler extends ZipArchive
{
    public static function errorMessage($code)
    {
        $zipMessages = array(
            ZipArchive::ER_EXISTS => 'File already exists.',
            ZipArchive::ER_INCONS => 'Zip archive inconsistent.',
            ZipArchive::ER_INVAL => 'Invalid argument.',
            ZipArchive::ER_MEMORY => 'Malloc failure.',
            ZipArchive::ER_NOENT => 'No such file.',
            ZipArchive::ER_NOZIP => 'Not a zip archive.',
            ZipArchive::ER_OPEN => 'Can\'t open file.',
            ZipArchive::ER_READ => 'Read error.',
            ZipArchive::ER_SEEK => 'Seek error.',
        );

        if (array_key_exists($code, $zipMessages)) {
            return $zipMessages[$code];
        } else {
            return sprintf('An unknown error has occurred(%s)', intval($code));
        }
    }
}
