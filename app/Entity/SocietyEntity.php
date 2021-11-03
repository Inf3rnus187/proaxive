<?php
/*
 * SocietyEntity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date October 15, 2021
 */
namespace App\Entity;

use src\Entity\Entity;

class SocietyEntity extends Entity
{
    /**
     * Permet de générer le lien d'édition du client
     *
     * @return string
     */
    public function getUrlAdmin(string $id = null, string $field = null)
    {
        if($id == null){
            $url = '/admin/society/' . $this->id . '/' . $field;
        } else {
            $url = '/admin/society/' . $id . '/' . $field;
        }
        return $url;
    }
}