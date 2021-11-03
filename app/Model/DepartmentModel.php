<?php
/*
 * DepartmentModel Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */

namespace App\Model;

use src\Model\Model;

/**
 * Description of DepartmentTable
 *
 * @author SelMaK
 */
class DepartmentModel extends Model{
    //put your code here
    protected $model = 'pe1x_departments'; // Générer un nom de table différent

        /**
     * Permet de vérifier si un nom/titre existe déjà dans la table
     *
     * @param $name
     * @return mixed
     */
    public function scanName($name){

        return $this->queryscan("SELECT id FROM " . $this->model . " WHERE name = ?", [$name]);

    }

}
