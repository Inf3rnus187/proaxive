<?php
/*
 * Entity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace src\Entity;

class Entity{

    public function __get($key){
        $method = 'get' . ucfirst($key);
        $this->$key = $this->$method();
        return $this->$key;

    }

    /**
     * Retourne un champ d'une ligne si non renvoi false
     *
     * @param $data
     * @param $text
     * @return mixed
     */
    public function getData($data, $text){
        if(empty($data)){
            return $data;
        }else{
            return $text;
        }
    }

    /**
     * Génère le label customisé
     * @param string $bgcolor
     * @param string $txtcolor
     * @param string $title
     * @return string
     */
    public function getLabelCustom(string $bgcolor, string $txtcolor, string $title){
        $var = '<span class="label" style="background-color:'.$bgcolor.';color:'.$txtcolor.'">'.$title.'</span>';
        return $var;
    }

    /**
     * Transformer un texte en URL
     * @param $item
     * @return string
     */
    public static function cleanText($item){
        // remplacer ces caractères ...
        $charSpec = array( 'à','â','ä','È','É','é','è','ê','ë','î','ï','ô','ù','û','ç' );
        // ... par ceux-ci
        $CharClean = array( 'a','a','a','E','E','e','e','e','e','i','i','o','u','u','c' );
        // Supprime de $titre les accents, trémas et cédilles
        $firstName = str_replace($charSpec, $CharClean, $item);
        // Convertit en minuscules
        $firstName = strtolower($firstName);
        // Remplace les caractères non-alphanumériques par des tirets
        $firstName = preg_replace("/[^A-Za-z0-9]/", '-', $firstName );
        // Remplace les tirets multiples par un tiret unique
        $firstName = preg_replace("/\-+/", '-', $firstName );
        // Supprime le dernier caractère si c'est un tiret
        $firstName = rtrim($firstName, '-');
        return $firstName;
    }

}