<?php
/*
 * AppController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */

namespace App\Controller;
use src\Controller\Controller;
use \App;

class AppController extends Controller
{
    public function loadModel($model_name){
        $this->$model_name = App::getInstance()->getModel($model_name);
    }

}