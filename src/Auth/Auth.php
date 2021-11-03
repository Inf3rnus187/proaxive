<?php
/*
 * Auth Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */

namespace src\Auth;

use App;
use src\MyClass\Cookie;
use src\MyClass\SendMail;
use src\MyClass\Session;

class Auth
{

    /**
     * @var session
     */
    private Session $session;

    /**
     * @var array
     */
    private $option = [
        'restriction_msg' => 'Permission refusée',
        'not_login' => '[Error_Login] Vous devez être authentifié !',
        'auth_perms' => '[Auth_Perms] Action refusée',
        'admin_perms' => '[Admin_Perms] Vous devez être Administrateur pour effectuer cete action'
    ];

    /**
     * @var
     */
    private $db;

    /**
     * @var Cookie
     */
    private Cookie $cookie;


    public function __construct($session, $option = [])
    {
        $this->session = $session;
        $this->db = App::getInstance()->getDB();
        $this->cookie = new Cookie();
        $this->option = [
            'restriction_msg' => 'Permission refusée !',
            'not_login' => '[Error_Login] Vous devez être authentifié !',
            'auth_perms' => '[Auth_Perms] Action refusée !',
            'admin_perms' => '[Admin_Perms] Vous devez être Administrateur pour effectuer cette action.'
        ];
    }


    /**
     * Récupère l'ID de l'utilisateur connecté
     *
     * @return mixed
     */
    public function getUserID() {
        if($this->logged()) {
            //return $this->session['auth']->id;
            //return $_SESSION['auth']->id;
            return $this->session->read('auth')->id;
        }
    }

    /**
     * Récupère le rôle id de l' utilisateur
     *
     * @return bool
     */
    public function getUserRid(){

        if($this->logged()){
            return $this->session->read('auth')->roles_id;
        }

        return false;
    }

    /**
     * Permet d'écrire la connexion en session
     *
     * @param $user
     */
    public function connect($user){
        $this->session->write('auth', $user);
    }

    /**
     * Permet de se connecter
     *
     * @param $db
     * @param $mail
     * @param $password
     * @param bool $remember
     * @return bool
     */
    public function login($mail, $password, $remember = false){
        $user = $this->db->prepare('SELECT * FROM pe1x_ausers as a LEFT JOIN pe1x_roles as r ON a.roles_id = r.id WHERE mail = ?', [$mail], null, true);
        if($user AND password_verify($password, $user->password)){
            if($user->key_totp != ''){
                $this->session->write('user_id', $user->a_id);
                header('Location: /login-Totp');
            } else {
                return $user;
            }
        }else{
            return false;
        }
    }

    /**
     * Login en deux phases
     * Courriel > Password
     * @param $db
     * @param $pseudo
     */
    public function login2($user, $password){
            if($user AND password_verify($password, $user->password)){
                if($user->key_totp != ''){
                    $this->session->write('token_id', $user->id);
                    header('Location: /login-Totp');
                } else {
                    return $user;
                }
            }else{
                return false;
            }
    }

    /**
     * Permet de vérifier si l'utilisateur est bien connecté
     *
     * @return bool
     */
    public function logged(){
        if(!$this->session->read('auth')){
            return false;
        }
        return $this->session->read('auth');
    }


    /**
     * Permet de créer un cookie de connexion
     *
     */
    public function connectFromCookie()
    {
        if(isset($_COOKIE['proaxive_auth']) && !isset($_SESSION['auth'])){
            $auth = $this->cookie->explode_cookie('proaxive_auth');
            $user_id = $this->db->prepare('SELECT * FROM pe1x_ausers WHERE id = ?', [$auth[0]], null, true);
            $key = $this->cookie->sha1_cookie($user_id->fullname . $user_id->password);
            if($key == $auth[1]){
                $this->cookie->set_cookie('proaxive_auth', $user_id->id, $key, time() + 60 * 60 * 24 * 7);
                $this->connect($user_id);
                header("Location: /admin");
                exit();
            }
        }

    }

    /**
     * Permet d'envoyer un mail de réinitialisation de mot de passe
     *
     * @param $db
     * @param $user_mail
     * @param $id
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function resetPassword($db, $user_mail, $id){
        if(isset($user_mail)){
            $token = sha1(uniqid(rand())); // Initialise un nouveau token
            $db->prepare('UPDATE pe1x_ausers SET token = ?, reset_at = NOW() WHERE id = ?', [$token, $id], null, true);
            // Envoie du mail
            $user_mail = htmlspecialchars(addslashes($user_mail));
            $token = htmlspecialchars(addslashes($token));
            $sendMail = new SendMail();
            if (getenv('MAIL_SERVICE') == 'mailjet'){
                $sendMail->sendDataMailJet('Réinitialisation du mot de passe', $user_mail, 'Proaxive', [
                    'token' => $token,
                    'user_mail' => $user_mail,
                    'id' => $id
                ], '/templates/mail_initpassword.twig');

            } elseif (getenv('MAIL_SERVICE') == 'swiftmailer') {
                $sendMail->sendDataSwiftMailer('Réinitialisation du mot de passe', $user_mail, $user_mail, 'Proaxive', [
                    'token' => $token,
                    'user_mail' => $user_mail,
                    'id' => $id
                ], '/templates/mail_initpassword.twig');
            }

        }
    }

    /**
     * Permet de réinitialiser le mot de passe
     *
     * @param $db
     * @param $uid
     * @param $password
     */
    public function initPwdAuth($db, $id, $password){
        $db->prepare('UPDATE pe1x_ausers SET password = ?, reset_at = NULL, token = NULL WHERE id = ?', [$password, $id], null, true);
    }

    /*****************************************************************
     * RESTRICTION UTILISATEUR
     * Ces methodes permettent de gérer la restriction utilisation
     *****************************************************************/

    /**
     * Permet de vérifier si l'utilisateur est connecté. Redirige vers login/signin sinon
     */
    public function restrict(){
        if(!$this->session->read('auth')){
            $this->session->setFlash('danger', $this->option['not_login']);
            header('Location: /login-dash');
            exit();
        }
    }

    /**
     * Permet d'accorder l'accès uniquement aux administrateurs
     */
    public function isAdminOnly(){
        if(!$this->session->read('auth') || $this->session->read('auth')->roles_id > 1){
            $this->session->setFlash('danger', $this->option['admin_perms']);
            header('Location: /admin/needperms');
            exit();
        }
    }
}