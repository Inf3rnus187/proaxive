<?php
/*
 * StatusModel Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace App\Model;

use src\Model\Model;

/**
 * Description of StatusTable
 *
 * @author SelMaK
 */
class StatusModel extends Model{

     protected $model = 'pe1x_status';
     
    /**
    *
    */

    public function last(){

        return $this->query("SELECT *
            FROM ".$this->model."
            ORDER BY title DESC");

    }

    /**
     * Permet de scanner le titre du status
     *
     * @param $name
     * @return mixed
     */
    public function scanTitle($name){

        return $this->queryscan("SELECT id FROM " . $this->model . " WHERE title = ?", [$name]);

    }
    
}
