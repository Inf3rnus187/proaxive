<?php
/*
 * AdminSettingController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace App\Controller\Dashboard;

use Creitive\Breadcrumbs\Breadcrumbs;
use PDO;
use src\Entity\Entity;
use src\Form\SpectreForm;
use src\MyClass\SendMail;
use src\MyClass\Session;
use Ifsnop\Mysqldump as IMysqldump;

class AdminSettingController extends AppController{


    private string $current_navitem;

    /**
     * @var Session
     */
    private Session $session;

    /**
     * @var Breadcrumbs
     */
    private Breadcrumbs $breadcrumbs;

    private string $current_menu;

    public function __construct()
    {
        parent::__construct();
        $this->current_navitem = 'config';
        $this->current_menu = 'setting';
        $this->session = Session::getSessionInstance();
        $this->breadcrumbs = new Breadcrumbs();
        // Models
        $this->loadModel('Company');
    }

    /**
     * Permet de lister les utilisateurs/clients
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function home(){
        $folderInstall = ROOT . '/install';
        if(!empty($_POST)){
            // Sauvegarde des nouveaux parametres
            if(isset($_POST['saveParameters'])){
                if(empty($_POST['php_error'])){$_POST['php_error'] = 0;}
                if(empty($_POST['view_version'])){$_POST['view_version'] = 0;}
                if(empty($_POST['full_error'])){$_POST['full_error'] = 0;}
                if(empty($_POST['notify_fixed'])){$_POST['notify_fixed'] = 0;}
                if(empty($_POST['fixed_dark'])){$_POST['fixed_dark'] = 0;}
                if(empty($_POST['sidebar_dark'])){$_POST['sidebar_dark'] = 0;}
                if(empty($_POST['aside_toggled'])){$_POST['aside_toggled'] = 0;}
                if(empty($_POST['fixed_header'])){$_POST['fixed_header'] = 0;}
                // Tile
                if(empty($_POST['tile_inter'])){$_POST['tile_inter'] = 0;}
                if(empty($_POST['tile_equipment'])){$_POST['tile_equipment'] = 0;}
                if(empty($_POST['tile_client'])){$_POST['tile_client'] = 0;}
                if(empty($_POST['tile_outlay'])){$_POST['tile_outlay'] = 0;}
                if(empty($_POST['tile_stickies'])){$_POST['tile_stickies'] = 0;}
                // Supprime le dernier / si présent de l'URL
                $writeJson = array(
                    'php_error' => $_POST['php_error'],
                    'full_error' => $_POST['full_error'],
                    'view_version' => $_POST['view_version'],
                    'style_selector' => 0,
                    'society_days' => $_POST['society_days'],
                    'society_hours' => $_POST['society_hours'],
                    'notify_fixed' => $_POST['notify_fixed'],
                    'fixed_dark' => $_POST['fixed_dark'],
                    'sidebar_dark' => $_POST['sidebar_dark'],
                    'fixed_header' => $_POST['fixed_header'],
                    'aside_toggled' => $_POST['aside_toggled'],
                    // Tile
                    'tile_inter' => $_POST['tile_inter'],
                    'tile_equipment' => $_POST['tile_equipment'],
                    'tile_client' => $_POST['tile_client'],
                    'tile_outlay' => $_POST['tile_outlay'],
                    'tile_stickies' => $_POST['tile_stickies'],
                    'app_url' => $_POST['app_url'],
                    'app_login_url' => $_POST['app_login_url']
                );
                if($writeJson){
                    file_put_contents(ROOT. '/config/setting.json', json_encode($writeJson));
                    $this->session->setFlash('success', "Les nouveaux paramètres ont été appliqués !");
                    return $this->redirect('/admin/setting');
                } else {
                    $this->session->setFlash('danger', "Un problème est survenu !");
                }
            }
            // Renomme le dossier d'installation
            if(isset($_POST['renameFolder'])){
               $renameFolder = rename(ROOT . "/install", ROOT. "/install" . rand(8, 99999));
               if($renameFolder){
                   $this->session->setFlash('success', "Le dossier <strong>install</strong> a bien été renommé !");
               }
            }


        }
        // Lecture du fichier de configuration json
        $varRoot = ROOT;
        $viewJsonFile = file_get_contents(ROOT. '/config/setting.json');
        $viewJson = json_decode($viewJsonFile, true);

        // Vérifie si le dossier d'installation Proaxive existe
        $checkFolderInstall = file_exists($folderInstall);

        // Check URL/Domaine
        $getUrl = $this->getBaseUrl();

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Paramètres', '/admin/setting')
            ->addCrumb('Paramètres globaux', '');
        $this->breadcrumbs->render();

        $form = new SpectreForm($viewJson);
        $this->render('dashboard/settings/home.twig', [
            'form' => $form,
            'varRoot' => $varRoot,
            'checkFolderInstall' => $checkFolderInstall,
            'current_navitem' => 'setting',
            'getUrl' => $getUrl,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);

    }

    /**
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function courriel(){
        $company = $this->Company->isDefault();
        if(!empty($_POST)){
            // Sauvegarde des nouveaux parametres
            if(isset($_POST['saveParameters'])){
                $writeJson = array(
                    'mail_title_from' => $_POST['mail_title_from'],
                    'mail_service' => $_POST['mail_service'],
                    'mail_address' => $_POST['mail_address'],
                    'mail_testaddress' => $_POST['mail_testaddress'],
                    'mail_host' => $_POST['mail_host'],
                    'mail_port' => $_POST['mail_port'],
                    'mail_username' => $_POST['mail_username'],
                    'mail_password' => $_POST['mail_password'],
                    'mail_encryption' => $_POST['mail_encryption'],
                    'mailjet_publickey' => $_POST['mailjet_publickey'],
                    'mailjet_privatekey' => $_POST['mailjet_privatekey'],
                    'mailjet_username' => $_POST['mailjet_username'],
                );
                if($writeJson){
                    file_put_contents(ROOT. '/config/mail.json', json_encode($writeJson));
                    $this->session->setFlash('success', "Les nouveaux paramètres ont été appliqués !");
                } else {
                    $this->session->setFlash('danger', "Un problème est survenu !");
                }
            }
            // Test mail
            if(isset($_POST['testMail'])){
                $sendMail = new SendMail();
                // Lecture du fichier de configuration json (Mail)
                $viewJsonFile = file_get_contents(ROOT. '/config/mail.json');
                $viewJson = json_decode($viewJsonFile, false);
                if (empty($viewJson->mail_testaddress)){
                    $this->session->setFlash('danger', "<strong>Erreur des services courriel :</strong> impossible de poursuivre, le courriel de test est manquant !");
                } else {
                    if ($viewJson->mail_service == 'mailjet'){
                        $sendMail->sendDataMailJet('TEST - Votre suivi intervention', $viewJson->mail_testaddress, 'John Doe', [
                            'data' => 'data'
                        ], 'templates/mail_updateintervention.twig');
                        $this->session->setFlash('success', "Le courriel de test a été envoyé !");
                    } elseif ($viewJson->mail_service == 'swiftmailer'){
                        $sendMail->sendDataSwiftMailer('TEST - Votre suivi intervention', $viewJson->mail_testaddress, $company->mail, 'John Doe', [
                            'data' => 'data'
                        ], 'templates/mail_updateintervention.twig');
                        $this->session->setFlash('success', "Le courriel de test a été envoyé !");
                    }
                }
            }
        }
        $viewJsonFile = file_get_contents(ROOT. '/config/mail.json');
        $viewJson = json_decode($viewJsonFile, true);

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Paramètres', '/admin/setting')
            ->addCrumb('Services Courriel', '');
        $this->breadcrumbs->render();

        $form = new SpectreForm($viewJson);
        $this->render('dashboard/settings/courriel.twig', [
            'form' => $form,
            'company' => $company,
            'current_navitem' => 'mailing',
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);

    }

    /**
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function database(){
        // Charge le fichier Json
        $viewJsonFile = @file_get_contents(ROOT. 'config/db.json');
        $viewJson = json_decode($viewJsonFile, true);

        if(!empty($_POST)){
            // Sauvegarde les nouveaux parametres
            if(isset($_POST['saveParameters'])){
                $this->saveSettingDatabase();
            }
            // Sauvegarde la base de données
            if(isset($_POST['saveDatabase'])){
                $this->mysqldumpData($viewJson);
            }
        }
        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Paramètres', '/admin/setting')
            ->addCrumb('Base de données', '');
        $this->breadcrumbs->render();
        $form = new SpectreForm($viewJson);
        $this->render('dashboard/settings/database.twig', [
            'form' => $form,
            'viewJson' => $viewJson,
            'current_navitem' => 'database',
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);

    }

    /**
     * Permet d'afficher les themes CSS d'un template
     * @param $theme
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function homeTheme(){

        $dir = ROOT. 'public/assets/styles';
        $folders = array_diff(scandir($dir), array('..', '.', 'default-css'));

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Paramètres', '/admin/setting')
            ->addCrumb('Thème CSS', '');
        $this->breadcrumbs->render();

        $this->render('dashboard/settings/themes/home.twig', [
            'folders' => $folders,
            'current_menu' => 'styles',
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    /**
     * Permet de modifier les themes CSS d'un template
     * @param $theme
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function viewCSS($theme){

        $theme = Entity::cleanText($theme);
        $dir = ROOT. 'public/assets/styles/' . $theme . '/stylesheets';
        if (file_exists($dir)){
            $files = array_diff(scandir($dir), array('..', '.'));
        } else {
            $this->session->setFlash('danger', "Impossible d'ouvrir le dossier <strong>$theme</strong>");
            return $this->homeTheme();
        }

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Paramètres', '/admin/setting')
            ->addCrumb('Thème CSS', '/admin/setting/css')
            ->addCrumb($theme, '');
        $this->breadcrumbs->render();

        $this->render('dashboard/settings/themes/folders.twig', [
            'files' => $files,
            'theme' => $theme,
            'current_menu' => 'styles',
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    /**
     * Permet de modifier un theme CSS
     * @param $theme
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function editCSS($theme, $css){

        $theme = Entity::cleanText($theme);
        $dir = ROOT. 'public/assets/styles/' . $theme . '/stylesheets/';
        $file = $dir . $css .'.css';
        if (file_exists($file)){
            $handle = $this->openFile($dir . $css.'.css', 'r');
        } else {
            $this->session->setFlash('danger', "Impossible d'ouvrir le fichier <strong>$css.css</strong>");
            return $this->homeTheme();
        }

        if(!empty($_POST)){
            if(isset($_POST['stylesheets'])){
                $stylesheets = $_POST['stylesheets'];
                // Vérifie si le fichier peut être écrit.
                if (is_writable($file)) {
                    if (!$handle = fopen($file, 'w+')) {
                        $this->session->setFlash('danger', "Impossible d'ouvrir le fichier ($file)");
                        exit;
                    }

                    if (fwrite($handle, $stylesheets) === FALSE) {
                        echo "Impossible d'écrire dans le fichier ($file)";
                        exit;
                    }

                    $this->session->setFlash('success', "<strong>[Rafraîchissement dans 3s]</strong> L'écriture dans le fichier CSS ($css) a réussi");
                    fclose($handle);
                    header("Refresh: 3;url=/admin/setting/css/$theme/$css");


                } else {
                    $this->session->setFlash('danger', "Le fichier $file n'est pas accessible en écriture.");

                }
            }
        }

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Paramètres', '/admin/setting')
            ->addCrumb('Thème CSS', '/admin/setting/css')
            ->addCrumb($theme, '/admin/setting/css/'.$theme)
            ->addCrumb($css, '');
        $this->breadcrumbs->render();

        $form = new SpectreForm($_POST);
        $this->render('dashboard/settings/themes/edit.twig', [
            'css' => $handle,
            'cssName' => $css,
            'form' => $form,
            'theme' => $theme,
            'current_menu' => 'styles',
            'breadcrumbs' =>$this->breadcrumbs
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
     * Permet de sauvegarder les connexion à la base de données (config Json)
     */
    private function saveSettingDatabase(){
        $writeJson = array(
            'db_name' => $_POST['db_name'],
            'db_host' => $_POST['db_host'],
            'db_user' => $_POST['db_user'],
            'db_passwd' => $_POST['db_passwd'],
        );
        // Vérifie si la connexion à la base de données est OK
        $postDB = $this->checkDB($_POST['db_name'], $_POST['db_host'], $_POST['db_user'], $_POST['db_passwd']);
        if($postDB != null){
            // OK !
            if($writeJson){
                file_put_contents(ROOT. 'config/db.json', json_encode($writeJson));
                $this->session->setFlash('success', "L'accès à la base de données a été mis à jour !");
            } else {
                $this->session->setFlash('danger', "Un problème est survenu !");
            }
        } else {
            // Erreur -> retour PDO $e
            $postDB;
        }
    }

    /**
     * Permet de sauvegarder la base de données de Proaxive
     */
    private function mysqldumpData($viewJson){
        if($viewJson != null){
            // Initialise les variables avec les données du Json
            $host = $viewJson['db_host'];
            $database = $viewJson['db_name'];
            $user = $viewJson['db_user'];
            $password = $viewJson['db_passwd'];
        }
        // Charge Mysqldump
        $dumper = new IMysqldump\Mysqldump('mysql:host='.$host.';dbname='. $database, $user, $password);
        // Envoi le fichier .sql vers le dossier de sauvegarde
        $dumper->start(ROOT.'db/save/dump-'.time().'.sql');
        if($dumper){
            $this->session->setFlash('success', 'La base de données a été sauvegardée (db/save) !');
        }
    }

}
