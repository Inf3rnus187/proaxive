<?php
/*
 * ClientEntity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace App\Entity;

use src\Entity\Entity;

/**
* Class UserEntity
*/

class ClientEntity extends Entity{

    /**
     * Permet de générer le lien d'édition du client
     *
     * @return string
     */
    public function getUrlAdmin(string $id = null, string $field = null)
    {
        if($id == null){
            $url = '/admin/client/' . $this->id . '/' . $field;
        } else {
            $url = '/admin/client/' . $id . '/' . $field;
        }
        return $url;
    }

    /**
     * Permet de générer un lien vers la fiche cliente
     *
     * @return string
     */
    public function getUrl()
    {
        return '/admin/client/'. $this->c_id;
    }

}
