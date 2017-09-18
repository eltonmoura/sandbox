<?php
namespace Sandbox;

use \Exception;

class ConfigManeger
{
    private static $config;

    const CONTEXT_VAR = 'APPLICATION_ENV';

    public static function getConfig()
    {
        $context = self::getContext();
        if (!isset(self::$ConfigManeger)) {
            $configFile = sprintf('%s/config/%s.ini', APPLICATION_DIR, $context);
            if (!is_file($configFile)) {
                throw new Exception(
                    sprintf(
                        "Erro: Não foi encontrado o arquivo de configuração '%s'",
                        $configFile
                    )
                );
            }

            // Faz o parse do arquivo e obtem um array
            $config = parse_ini_file($configFile, true);

            // Transforma o array em um objeto multidimensional. O '(object)' não faz recursivamente.
            self::$config = json_decode(json_encode($config, false));
        }
        return self::$config;
    }

    public static function getContext()
    {
        if (!($context = getenv(self::CONTEXT_VAR))) {
            throw new Exception(
                sprintf(
                    "Erro: É necessessário configurar a variável de ambiente '%s'",
                    self::CONTEXT_VAR
                )
            );
        }
        return $context;
    }
}
