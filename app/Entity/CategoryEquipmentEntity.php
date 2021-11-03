<?php
/*
 * CategoryEquipmentEntity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */

namespace App\Entity;

use src\Entity\Entity;

class CategoryEquipmentEntity extends Entity
{
    /**
     * Permet de générer le lien d'édition du client
     *
     * @return string
     */
    public function getUrlAdmin(string $id = null, string $field = null)
    {
        if($id == null){
            $url = '/admin/equipments/category/' . $this->id . '/' . $field;
        } else {
            $url = '/admin/equipments/category/' . $id . '/' . $field;
        }
        return $url;
    }
}