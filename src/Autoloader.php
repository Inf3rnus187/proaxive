<?php
/*
 * Autoloader Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace src;

class Autoloader
{
    static function register(){

        spl_autoload_register(array(__CLASS__, 'autoload'));

    }

    static function autoload($class){
        if(strpos($class, __NAMESPACE__ . '\\') === 0){
            $class = str_replace(__NAMESPACE__ . '\\', '', $class);
            $class = str_replace('\\', '/', $class);
            require __DIR__ . '/' . $class . '.php';
        }
    }
}