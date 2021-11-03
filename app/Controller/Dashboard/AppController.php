<?php
/*
 * AppController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace App\Controller\Dashboard;

use App;

class AppController extends App\Controller\AppController
{

    public function __construct()
    {
        parent::__construct();
        App::getAuth()->restrict();
    }
}