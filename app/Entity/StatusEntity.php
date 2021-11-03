<?php
/*
 * StatusEntity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace App\Entity;
use src\Entity\Entity;

/**
 * Description of StatusEntity
 *
 * @author SelMaK
 */
class StatusEntity extends Entity{

    /**
     * Genere une URL de l'item Post
     */

    public function geturlAdmin(){

        $url = '/admin/edit-status/'. $this->id;
        return $url;

    }
    /**
     * Explore les statuts
     * @return
     */

    public function getStatus(){
        $var = '<span class="label" style="background-color:'.$this->bgcolor.';color:'.$this->txtcolor.'">'.$this->title.'</span>';
        return $var;

    }
}
