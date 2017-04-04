<?php
namespace Sandbox;

class Str
{
    private $value;
    private $length;

    public function __construct($str)
    {
        $this->value = $str;
        $this->length = strlen($str);
    }

    public function __toString()
    {
        return $this->value;
    }

    public static function asSlug($text)
    {
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        if (empty($text)) {
            return 'n';
        }
        return $text;
    }
}
