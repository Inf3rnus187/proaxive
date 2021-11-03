<?php
/*
 * Session Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace src\MyClass;

use src\Auth\Auth;

class Session
{
    static $instance;


    public static function getSessionInstance() {
        if(!self::$instance){
            self::$instance = new Session();
        }
        return self::$instance;
    }

    public function __construct(){
        $this->sessionArray = array(
            'secure' => false,
            'httponly' => true,
            'samesite' => ''
        );
        if (!isset($_SESSION)) {
            $this->sessionStart('sl_proaxive');
        }
    }

    /**
     * Session Start
     * @param string $name
     * @param int $lifetime
     */
    public function sessionStart(string $name, int $lifetime = null){
        session_set_cookie_params($this->sessionArray);
        session_name($name);
        session_start();
        session_get_cookie_params();
    }

    public function setFlash($class, $message){
        $_SESSION['flash'][$class] = $message;
    }

    public function hasFlashes(){
        return isset($_SESSION['flash']);
    }

    public function getFlashes(){
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }

    public function write($key, $value){
        $_SESSION[$key] = $value;
    }

    public function read($key){
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function delete($key){
        unset($_SESSION[$key]);
    }
}