<?php
/*
 * GettingStartedController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date September 18, 2021
 */
namespace App\Controller;

use PDO;
use src\Form\SpectreForm;
use src\MyClass\Session;

class GettingStartedController extends AppController
{

    private Session $session;

    public function __construct(){
        // Chargements des tables SQL
        parent::__construct();
        $this->session = Session::getSessionInstance();
    }


    /**
     * Permet de sauvegarder la connexion à la base de données
     * FIRST INSTALL
     */
    public function firstInstall(){
        // Charge le fichier Json
        $viewJsonFile = @file_get_contents(ROOT. 'config/db.json');
        $viewJson = json_decode($viewJsonFile, false);
        if($viewJson->first_install == 0 || empty($viewJson->db_host)){
            header('Location: /');
            exit();
        }
        if(!empty($_POST)){
            // Sauvegarde des nouveaux parametres
                $writeJson = array(
                    'db_name' => $_POST['db_name'],
                    'db_host' => $_POST['db_host'],
                    'db_user' => $_POST['db_user'],
                    'db_passwd' => $_POST['db_passwd'],
                    'first_install' => 0
                );
            $this->inputValide('db_name');
            $this->inputValide('db_host');
            $this->inputValide('db_user');
            // Vérifie si la connexion à la base de données est OK
                $postDB = $this->checkDB($_POST['db_name'], $_POST['db_host'], $_POST['db_user'], $_POST['db_passwd']);
                if($postDB != null){
                    // OK !
                    if($writeJson){
                        file_put_contents(ROOT. 'config/db.json', json_encode($writeJson, JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS));
                        $this->session->setFlash('success', "La configuration a été prise en compte !");
                        $this->redirect($this->app_setting()->app_login_url);
                    } else {
                        $this->session->setFlash('danger', "Un problème est survenu !");
                    }
                } else {
                    // Erreur -> retour PDO $e
                    $postDB;
                }
        }
        $form = new SpectreForm($_POST);
        $this->render('home/gettingstarted.twig', [
            'form' => $form
        ]);
    }

    /**
     * $db_name, $db_host, $db_user, $db_passwd
     * @param $db_name
     * @param $db_host
     * @param $db_user
     * @param $db_passwd
     * @return PDO
     */
    private function checkDB($db_name, $db_host, $db_user, $db_passwd){
        try{
            $db = new PDO('mysql:dbname=' . $db_name . ';host='.$db_host . '', $db_user, $db_passwd);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db;
        } catch (\PDOException $e){
            $this->session->setFlash('danger', 'Impossible de se connecter à la base de données, <strong>veuillez vérifier vos informations de connexion</strong> : '. $e->getMessage());
        }
    }


    /**
     * Vérifie les champs du formulaire
     * @param $name
     */
    private function inputValide($name){
        $var = filter_input(INPUT_POST, $name, FILTER_SANITIZE_ENCODED);
        if($var == null){
            $this->session->setFlash('danger', "Ce n'est pas une entrée valide !");
            $this->redirect('/getting-started');
        }
    }


}