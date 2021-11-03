<?php
/*
 * UserFormController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace App\Controller;

use App;
use Otp\Otp;
use ParagonIE\ConstantTime\Encoding;
use src\Auth\Auth;
use src\Form\MaterializeForm;
use src\Form\SpectreForm;
use src\MyClass\Cookie;
use src\MyClass\Gravatar;
use src\MyClass\Session;
use src\MyClass\Str;

class UserFormController extends AppController
{

    /**
     * @var session
     */
    private $session;

    /**
     * @var Auth
     */
    private Auth $auth;

    /**
     * @var
     */
    private $db;

    /**
     * @var Cookie
     */
    private Cookie $cookie;

    public function __construct(){
        // Check First Install
        App::getConfigDatabase();
        $this->session = Session::getSessionInstance();
        $this->auth = App::getAuth();
        $this->db = App::getInstance()->getDB();
        $this->cookie = new Cookie();
        parent::__construct();
        // Chargements des tables SQL
        $this->loadModel('User');
    }

    /**
     * Permet de se connecter à l'espace utilisateur
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function login()
    {
        $this->auth->connectFromCookie();

        // Si la session login_data existe
        if($this->session->read('login_data')){
            $this->redirect('/login');
        }

        if(!empty($_POST) && !empty($_POST['mail'])){
            $user = $this->User->loginEmail($_POST['mail']);
            // $user = $this->auth->loginWithoutTotp($this->db, $_POST['mail']);
            if(!$user){
                $this->session->setFlash('danger', "Ce compte n'existe pas !");
            }else{
                $this->session->write('login_data', $user);
                $this->redirect('/login');
            }
        }
        $form = new SpectreForm($_POST);
        $this->render('login/home.twig', [
            'form' => $form
        ]);
    }

    /**
     *
     */
    public function connectWithPassword(){
        $auth = new Auth(app::getInstance()->getDB());

        if($this->auth->logged()){$this->redirect('/admin');}

        if(!$this->session->read('login_data')){
            $this->session->setFlash('danger', "Votre session à expirée !");
            return $this->redirect('/login-dash');
        }

        $user = $this->User->find('id', (int) $this->session->read('login_data')->id);
        // Gravatar
        $gravatar = new Gravatar();
        $gravatar->setEmail($user->mail);
        $avatar = $gravatar->getSrc();
        $key = $this->cookie->sha1_cookie($user->fullname . $user->password);

        if(!empty($_POST)){
            if(!empty($_POST['password'])){
                if(isset($_POST['remember'])){
                    $this->cookie->set_cookie('proaxive_auth', $user->id, $key, time() + 60 * 60 * 24 * 7);
                }
                $userLogin = $this->auth->login2($user, $_POST['password']);
                if($userLogin){
                    $auth->connect($userLogin);
                    $this->redirect('/admin');
                }else{
                    $this->session->setFlash('danger', "Désolé, le mot de passe ne correspond pas !");
                }
            }
        }

        $form = new SpectreForm($_POST);
        $this->render('login/connecting.twig', [
            'form' => $form,
            'avatar' => $avatar,
            'user' => $user
        ]);
    }


    /**
     * Permet de se connecter avec l'authentification à double facteurs
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function loginTotp(){

        if(!$this->session->read('token_id')){
            return $this->login();
        }
        $user = $this->User->find('id', $this->session->read('token_id'));
        $otp = new Otp();
        if(!empty($_POST)){
            if($otp->checkTotp(Encoding::base32DecodeUpper($user->key_totp), $_POST['code'])){
                $auth = new Auth(app::getInstance()->getDB());
                $auth->connectFromCookie();
                $auth->connect($user);
                //$this->session->setFlash('success', "Vous êtes connecté !");
                $this->redirect('/admin');
            } else {
                $this->session->setFlash('danger', 'Ce code ne correspond pas !');
            }
        }

        $form = new SpectreForm($_POST);
        $this->render('login/logintotp.twig', [
            'form' => $form
        ]);
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function reset(){
        $auth = App::getAuth();
        $db = App::getInstance()->getDB();
        # Vérifie si l'utilisateur est connecté
        if($auth->logged()){$this->redirect('/admin');}
        # Vérifie le formulaire et envoi le lien de réinitialisation
        if(!empty($_POST) && !empty($_POST['email'])){
            # Vérifie si c'est bien une adresse email
            $scanMail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            # Scan la base de donnée afin de recherche l'adresse email
            $verifyEmail = $this->User->scanMail($scanMail);
            if($verifyEmail){
                $auth->resetPassword($db, $scanMail, $verifyEmail->id);
                $this->session->setFlash('success', "<strong>Succès</strong> Les instructions du rappel de mot de passe vous ont été envoyées par courriel.");
            } else {
                $this->session->setFlash('danger', "<strong>Erreur</strong> Aucun profil ne corresponde à cette adresse.");
            }

        }
        $form = new SpectreForm($_POST);
        $this->render('login/lostpassword.twig', [
            'form' => $form
        ]);
    }

    /**
     * @param $getToken
     * @param int $id
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function init($token, int $id)
    {
        $auth = App::getAuth();
        $db = App::getInstance()->getDB();

        $user = $this->User->scanToken($id, $token);

        if($user){
            if(!empty($_POST)){
                $password = htmlspecialchars($_POST['password']);
                $password_confirm = htmlspecialchars($_POST['password_confirm']);
                if(isset($password) && $password_confirm){
                    $password_hash = password_hash($password, PASSWORD_BCRYPT);
                    $auth->initPwdAuth($db, $id, $password_hash);
                    $this->session->setFlash('success', "<strong>Réinitialisé</strong> Votre mot de passe a été changé, vous pouvez maintenant vous connecter.");
                    return $this->redirect('/'.$this->app_setting()->app_login_url);
                } else {
                    $this->session->setFlash('danger', "<strong>Erreur</strong> Un compte sans mot de passe ? Voyons, restons sérieux !");
                }
            }
        } else {
            $this->session->setFlash('danger', "<strong>Erreur</strong> Ce token n'est pas valide.");
            return $this->reset();
        }

        $form = new SpectreForm($_POST);
        $this->render('login/init.twig', [
            'form' => $form
        ]);
    }

}