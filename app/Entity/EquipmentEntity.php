<?php
/*
 * EquipmentEntity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */

namespace App\Entity;


use src\Entity\Entity;

class EquipmentEntity extends Entity
{

    /**
     * Génère le lien Admin pour l'édition
     * @param string|null $id
     * @return string
     */
    public function geturlAdmin(string $id = null, string $field = null) {
        if($id == null){
            $url = '/admin/equipment/' . $this->id . '/' . $field;;
        } else {
            $url = '/admin/equipment/' . $id . '/' . $field;;
        }
        return $url;
    }

    public function getEtat()
    {
        if ($this->status == 1) {
            return '<span class="badge badge-light-success">Fonctionnel</span>';
        } else {
            return '<span class="badge badge-light-danger">Hors-Service</span>';
        }
    }

    public function getworkshop()
    {
        if ($this->inworkshop == 1) {
            return '<span class="badge badge-light-info">En atelier</span>';
        } else {
            return '<span class="badge badge-light-success">Sortie</span>';
        }
    }

}