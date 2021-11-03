<?php
/*
 * OperatingSystemEntity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace App\Entity;
use src\Entity\Entity;

class OperatingSystemEntity extends Entity
{
    /**
     * Permet de générer le lien d'édition du client
     *
     * @return string
     */
    public function getUrlAdmin(string $id = null, string $field = null)
    {
        if($id == null){
            $url = '/admin/operating-system/' . $this->id . '/' . $field;
        } else {
            $url = '/admin/operating-system/' . $id . '/' . $field;
        }
        return $url;
    }
}