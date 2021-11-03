<?php
/*
 * DepartmentEntity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace App\Entity;

use src\Entity\Entity;

/**
 * Description of DepartmentEntity
 *
 * @author SelMaK
 */
class DepartmentEntity extends Entity {


    /**
     * Genere une URL de l'item Post
     */

    public function geturlAdmin(){

        $url = '/admin/edit-department/'. $this->id;
        return $url;

    }
}
