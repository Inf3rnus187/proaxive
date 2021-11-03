<?php
/*
 * BrandModel Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */

namespace App\Model;

use src\Model\Model;

class BrandModel extends Model
{
    protected $model = 'pe1x_brands'; // Générer un nom de table différent

    /**
     * Permet de vérifier si un nom/titre existe déjà dans la table
     *
     * @param $name
     * @return mixed
     */
    public function scanTitle($name){

        return $this->queryscan("SELECT id FROM " . $this->model . " WHERE b_title = ?", [$name]);

    }
}