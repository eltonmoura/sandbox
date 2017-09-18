<?php
namespace Sandbox;

use Katzgrau\KLogger\Logger;
use Psr\Log\LogLevel;

class LoggerSingleton
{
    private static $logger;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!isset(self::$logger)) {
            self::$logger =  new Logger('/var/log/sandbox/', LogLevel::DEBUG, ['dateFormat' => 'c']);
        }
        return self::$logger;
    }
}
