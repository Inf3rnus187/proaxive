<?php
/*
 * UserController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */

namespace App\Controller;


use App;
use src\Auth\Auth;
use src\MyClass\Cookie;
use src\MyClass\Session;

class UserController extends AppController
{

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var Cookie
     */
    private Cookie $cookie;

    public function __construct(){
        // Chargements des tables SQL
        parent::__construct();
        $this->session = Session::getSessionInstance();
        $this->cookie = new Cookie();
    }

    /**
     * Permet de détruire toutes les session + cookie et d'être redirigé vers l'accueil du site
     */
    public function logout()
    {
        $this->session->delete('auth');
        $this->cookie->delete('proaxive_auth');
        $this->session->delete('login_data');
        $this->session->setFlash('success', "Vous êtes maintenant déconnecté");
        $this->redirect($this->app_setting()->app_login_url);
    }

    /**
     * Permet de verrouiller l'accès admin (demande simple de mot de passe pour se reconnecter)
     */
    public function locked(){
        $this->session->delete('auth');
        $this->session->setFlash('success', "Vous accès est maintenant verrouillé !");
        $this->redirect('/login');
    }
}