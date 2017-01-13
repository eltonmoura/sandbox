<?php
namespace Sandbox;

use Katzgrau\KLogger\Logger;

class LoggerSingleton
{
    private static $logger;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (!isset(self::$logger)) {
            self::$logger =  new Logger(APPLICATION_DIR . '/logs');
        }
        return self::$logger;
    }
}
