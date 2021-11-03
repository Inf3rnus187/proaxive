<?php
/*
 * SettingEntity Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */

namespace App\Entity;


use src\Entity\Entity;

class SettingEntity extends Entity
{

    public function getUrl(){

        return '/admin/setting/css/' . $this->theme . '/' . $this->file;
    }
}