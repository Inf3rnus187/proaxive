<?php
/*
 * AdminMyAccountController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date October 18, 2021
 */
namespace App\Controller\Dashboard;

use App;
use src\Auth\Auth;
use Creitive\Breadcrumbs\Breadcrumbs;
use Otp\GoogleAuthenticator;
use Otp\Otp;
use ParagonIE\ConstantTime\Base32;
use ParagonIE\ConstantTime\Encoding;
use src\Form\SpectreForm;
use src\MyClass\Session;

class AdminMyAccountController extends AppController
{

    /**
     * @var string
     */
    private $current_navitem;
    /**
     * @var Auth
     */

    private Auth $auth;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var Breadcrumbs
     */
    private Breadcrumbs $breadcrumbs;


    public function __construct(){

        parent::__construct();
        $this->session = Session::getSessionInstance();
        $this->auth = new Auth(app::getInstance()->getDB());
        $this->current_navitem = 'myaccount';
        $this->breadcrumbs = new Breadcrumbs();
        // Charge les tables de la base de données
        $this->loadModel('User');
        $this->loadModel('Roleauser');
        $this->loadModel('Intervention');
        $this->loadModel('Outlay');
    }

    /**
     * Permet de lister les administrateurs
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function home(){
        $auser = $this->User->find('id', (int) $this->auth->getUserId());
        $role = $this->Roleauser->find('id', $auser->roles_id);
        $interventions = $this->Intervention->allByUserWithEquipmentStatusAndCategory($auser->id);
        $outlay = $this->Outlay->allById('auser_id', $auser->id);

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Utilisateurs', '')
            ->addCrumb('Mon Compte', '');
        $this->breadcrumbs->render();

        $this->render('ausers/admin/myaccount/home.twig', [
            'auser' => $auser,
            'role' => $role,
            'current_navitem' => 'home',
            'interventions' => $interventions,
            'outlay' => $outlay,
            'breadcrumbs' => $this->breadcrumbs
        ]);

    }

    /**
     * Permet d'éditer un administrateur
     *
     * @return
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function edit(){
        $date = date('Y-m-d H:i:s');
        if(!empty($_POST)){
            if(!empty($_POST['pseudo']))
            {
                // Met à jour les données du compte administrateur
                $result = $this->User->update(
                    'id', (int) $this->auth->getUserId(),
                    [
                        'pseudo' => $_POST['pseudo'],
                        'fullname' => $_POST['fullname'],
                        'mail' => $_POST['mail'],
                        'address_ip' => $_POST['address_ip'],
                        'resetpassword' => false,
                        'lastvisite' => $date,
                        'token' => null,
                        'updated_at' => $date
                    ]);
                if($result){
                    $this->session->setFlash('success', "La modification de votre compte a été effectuée.");
                    return $this->home();
                }
                // Fin de la mise à jour du compte administrateur
            }
        }

        $auser = $this->User->find('id', (int) $this->auth->getUserId());
        $role = $this->Roleauser->find('id', $auser->roles_id);
        // Vérifie si le compte administrateur existe dans la base de données
        if(empty($auser)){
            $this->session->setFlash('danger', "Ce compte administrateur n'existe pas.");
            return $this->home();
        }


        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Utilisateurs', '/admin/ausers')
            ->addCrumb('Mon compte', '');
        $this->breadcrumbs->render();
        $form = new SpectreForm($auser);
        $this->render('ausers/admin/myaccount/edit.twig', [
            'auser' => $auser,
            'role' => $role,
            'form' => $form,
            'current_navitem' => 'edit',
            'breadcrumbs' => $this->breadcrumbs
        ]);

    }

    /**
     * Permet d'éditer un administrateur
     *
     * @return
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function password(){
        $date = date('Y-m-d H:i:s');
        if(!empty($_POST)){
            // Modifie le mot de passe du compte administrateur
            if(isset($_POST))
            {
                $confirmpassword = $_POST['confirmpassword'];
                $password = $_POST['password'];
                // Permet de vérifier si le mot de passe correspond
                if($password != $confirmpassword)
                {
                    $this->session->setFlash('danger', "Le mot de passe ne correspond pas !");

                }else{
                    // Permet de crypter le mot de passe
                    $pass_hash = password_hash($password, PASSWORD_BCRYPT);
                    // Envoi les données à la table
                    $updatepassword = $this->User->update(
                        'id', (int) $this->auth->getUserId(),
                        [
                            'password' => $pass_hash,
                            'token' => null,
                            'updated_at' => $date
                        ]);
                    if($updatepassword){
                        $this->session->setFlash('success', "Le mot de passe a été changé. Il sera effectif à votre prochaine connexion.");
                        return $this->home();
                    }
                }
                // Fin de vérification du mot de passe

            }
            // Fin de la function edition de mot de passe
        }

        $auser = $this->User->find('id', (int) $this->auth->getUserId());
        $role = $this->Roleauser->find('id', $auser->roles_id);
        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Utilisateurs', '')
            ->addCrumb('Mon compte', '');
        $this->breadcrumbs->render();
        $form = new SpectreForm($auser);
        $this->render('ausers/admin/myaccount/password.twig', [
            'auser' => $auser,
            'role' => $role,
            'form' => $form,
            'current_navitem' => 'password',
            'breadcrumbs' => $this->breadcrumbs
        ]);

    }

    /**
     * Permet d'activer l'authentification à double facteur
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function totp(){
        if(!empty($_POST)){
            if(isset($_POST['code'])){
                $otp = new Otp();
                if($otp->checkTotp(Encoding::base32DecodeUpper($this->session->read('secret')), $_POST['code'])){
                    $this->User->update(
                        'id', (int) $this->auth->getUserId(),
                        [
                            'key_totp' => $this->session->read('secret')
                        ]);
                    $this->session->delete('secret');
                    $this->session->setFlash('success', "L'authentification à double facteurs a bien été activée !");
                    return $this->edit($this->auth->getUserId());
                } else {
                    $this->session->setFlash('danger', 'Ce code ne correspond pas !');
                }
            }
        }

        $secret = GoogleAuthenticator::generateRandom();
        $this->session->write('secret', $secret);
        $qrcode = GoogleAuthenticator::getQrCodeUrl('totp', 'ProaxiveLite', $secret);

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Utilisateurs', '')
            ->addCrumb($this->session->read('auth')->pseudo, '/admin/myaccount')
            ->addCrumb('Authentification à double facteur', '');
        $this->breadcrumbs->render();

        $form = new SpectreForm($_POST);
        $this->render('ausers/admin/totp.twig', [
            'qrcode' => $qrcode,
            'secret' => $secret,
            'form' => $form,
            'current_navitem' => $this->current_navitem,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    /**
     * Permet de désactiver l'authentification à double facteur
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function totpDesactivate(){
        if(!empty($_POST)){
            if(isset($_POST['key_totp'])){
                $result = $this->User->update(
                    'id', (int) $this->auth->getUserId(),
                    [
                        'key_totp' => null
                    ]);
                if($result){
                    $this->session->setFlash('success', "L'authentification à double facteurs a bien été désactivée !");
                    return $this->home();
                } else {
                    $this->session->setFlash('danger', "Erreur lors de la mise à jour de la base de données !");
                }

            }
        }
    }
}