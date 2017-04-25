<?php
namespace Sandbox;

use \Exception;

class ConfigManeger
{
    private static $config;

    public static function getConfig()
    {
        if (!isset(self::$ConfigManeger)) {
            if (!($context = getenv('CONTEXT'))) {
                throw new Exception("Erro: É necessessário configurar a variável de ambiente 'CONTEXT'");
            }
            $configFile = sprintf('%s/config/%s.ini', APPLICATION_DIR, $context);
            if (!is_file($configFile)) {
                throw new Exception(sprintf("Erro: Não foi encontrado o arquivo de configuração '%s'", $configFile));
            }
            self::$config = json_decode(json_encode(parse_ini_file($configFile, true), false));
        }
        return self::$config;
    }
}
