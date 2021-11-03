<?php
/*
 * StickyModel Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.1
 * @date September 15, 2021
 */

namespace App\Model;

use src\Model\Model;

class StickyModel extends Model
{
    protected $model = 'pe1x_stickies'; // Générer un nom de table différent

    public function selectByStick(int $limit){
       return $this->query('SELECT * FROM '.$this->model.' WHERE stick = 1 ORDER BY created_at DESC LIMIT 0, '. $limit);
    }
}