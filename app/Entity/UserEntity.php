<?php
/*
 * UserEntity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace App\Entity;
use src\Entity\Entity;

class UserEntity extends Entity
{

    /**
     * Permet de générer le lien d'édition
     *
     * @return string
     */
    public function getUrlAdmin(string $id = null)
    {
        if($id == null){
            $url = '/admin/auser/' . $this->id . '/edit';
        } else {
            $url = '/admin/auser/' . $id . '/edit';
        }
        return $url;
    }
}