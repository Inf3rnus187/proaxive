<?php
/*
 * Str Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace src\MyClass;

class Str{


	static function random($length){
        $string = "";
        $chaine = "0123456789abcdefghijklmnpqrstuvwxy";
        srand((double)microtime()*1000000);
        for($i = 0; $i < $length; $i++) {
            $string .= $chaine[rand()%strlen($chaine)];
        }
        return $string;
	}

    static function limitCharacter($string, $max = 215){
        // on enlève les balises html
        $string = strip_tags($string);

        // on compte si le nombre de caractère est supp ou égale a max
        if (strlen($string) >= $max)
        {
            // on prend la chaine de 0 à max
            $string = substr($string, 0, $max);
            // on regarde ou se trouve le dernier espace dans la chaine
            $espace = strrpos($string, " ");
            // on prend la chaine de 0 au dernier espace et on ajoute ...
            $string = substr($string, 0, $espace)." ...";

            return false;
        } else {
            return $string;
        }

    }
}

