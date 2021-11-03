<?php
/*
 * SocietyModel Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date September 14, 2021
 */
namespace App\Model;

use src\Model\Model;

class SocietyModel extends Model
{

    protected $model = 'pe1x_society';

    /**
     * Permet de récupérer tous les équipements
     *
     * @return mixed
     */
    public function exportSociety(){

        return $this->queryAssoc("
            SELECT 
            *
            FROM ".$this->model." as s
            ORDER BY s.s_name DESC", false);
    }

}