<?php
namespace Sandbox;

use \PDO;
use Sandbox\ConfigManeger;

class Database
{
    public static $instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (!(self::$instance instanceof PDO)) {
            $config = ConfigManeger::getConfig();
            self::$instance = new PDO(
                sprintf(
                    'mysql:host=%s;dbname=%s;port=%s',
                    $config->db->host,
                    $config->db->dbname,
                    $config->db->port
                ),
                $config->db->username,
                $config->db->password
            );
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}
