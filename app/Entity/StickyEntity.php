<?php
/*
 * StickyEntity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date September 15, 2021
 */

namespace App\Entity;


use src\Entity\Entity;

class StickyEntity extends Entity
{
    /**
     * Genere une URL de l'item Post
     */

    public function getUrlAdmin(){

        $url = '/admin/sticky/'. $this->id;
        return $url;

    }
}