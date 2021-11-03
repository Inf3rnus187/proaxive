<?php
/*
 * Cookie Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date September 06, 2021
 */
namespace src\MyClass;

class Cookie
{

    /**
     * @param string $key
     * @param int $id
     * @param string $crypt
     * @param string $time
     */
    public function set_cookie(string $key, int $id, string $crypt, string $time){
        setcookie($key, $id . '=====' . $crypt, $time, '/', false, false, true);
    }

    /**
     * Récupère le sha1 avec REMOTE ADDR
     * @param string $crypt
     * @return string
     */
    public function sha1_cookie(string $crypt){
        return sha1($crypt);
    }

    /**
     * @param $key
     */
    public function delete($key){
        setcookie($key, null, time() -3600, '/', false, false, true);
    }

    /**
     * Explose la valeur du cookie afin de récupérer un tableau
     * @param string $key
     * @return false|string[]
     */
    public function explode_cookie(string $key){
        $data = $_COOKIE[$key];
        return explode('=====', $data);
    }
}