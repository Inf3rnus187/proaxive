<?php
/*
 * Config Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
use Symfony\Component\Dotenv\Dotenv;

class Config
{

    private static $envLoaded = false;

    /**
     * Charge le fichier de configuration
     */
    private static function loadEnv()
    {
        if(self::$envLoaded) {
            return;
        }
        $dotenv = new Dotenv();
        $dotenv->load(dirname(__DIR__) . '/.env');
        self::$envLoaded = true;
    }
}
