<?php
namespace Sandbox;

class ConfigManeger
{
    private $config;
    
    public function __construct($context = 'development')
    {
        $this->config = parse_ini_file(sprintf('%s/config/%s.ini', APPLICATION_DIR, $context), true);
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->config)) {
            return (object) $this->config[$name];
        }
    }
}
