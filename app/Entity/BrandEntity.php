<?php
/*
 * BrandEntity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */

namespace App\Entity;


use src\Entity\Entity;

class BrandEntity extends Entity
{
    /**
     * Permet de générer le lien d'édition
     *
     * @return string
     */
    public function getUrlAdmin(string $id = null)
    {
        if($id == null){
            $url = '/admin/equipments/edit-brand/' . $this->id;
        } else {
            $url = '/admin/equipments/edit-brand/' . $id;
        }
        return $url;
    }
}