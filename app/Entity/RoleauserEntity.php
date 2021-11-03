<?php
/*
 * RoleAuserEntity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace App\Entity;

use src\Entity\Entity;

class RoleauserEntity extends Entity{

    /**
    * Genere une URL de l'item Post
    */
    
    public function geturlAdmin(){

        $url = '/admin/ausers/role/'. $this->id;
        return $url;
        
    }
    

	}
