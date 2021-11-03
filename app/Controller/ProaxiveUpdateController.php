<?php
/*
 * ProaxiveUpdateController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date Jun 16, 2021
 */

namespace App\Controller;


use App;
use PDO;
use src\Form\SpectreForm;
use src\MyClass\Paginator;
use src\MyClass\Session;
use Ifsnop\Mysqldump as IMysqldump;

class ProaxiveUpdateController extends AppController
{
    private $current_menu;

    /**
     * @var Session
     */
    private $session;

    public function __construct(){

        parent::__construct();
        $this->current_menu = 'home';
        $this->session = Session::getSessionInstance();
        // Charge les différentes tables de la base de données
        $this->loadModel('ProaxiveUpdate');
        $this->loadModel('User');
        App::getAuth()->restrict();

    }

    /**
     * Permet d'afficher la liste des interventions
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function home(){
        $this->render('dashboard/home/home.twig');
    }

    /**
     * 
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function updateDatabase(){

        if(!empty($_POST['number_version'])){
            $result = $this->User->createFields([
                'key_totp VARCHAR(100) NULL'
            ]);
            /*$update = $this->Intervention->updateFields([
                'received datetime NULL'
            ]);*/
            if($result){
                $this->session->setFlash('success', 'La base de données a été mise à jour !');
            }
        }
        $form = new SpectreForm($_POST);
        $this->render('dashboard/update/model.twig', [
            'form' => $form,
            'current_menu' => $this->current_menu
        ]);
    }

    public function updateByFiles(){
        // Importe les données du fichier .sql vers la base de données
        if (!empty($_POST['importSql'])) {
            $db = new PDO('mysql:dbname=' . $this->readJson()->db_name . ';host='.$this->readJson()->db_host . '', $this->readJson()->db_user, $this->readJson()->db_passwd);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->query("SET NAMES 'utf8', lc_time_names = 'fr_FR'");
            // Récupère le fichier .sql
            $query = file_get_contents(ROOT . '/db/update1601_to_1602.sql');
            // Execute la tâche
            if (!$db->exec($query)){
                $this->session->setFlash('success', '<strong>Terminé !</strong> La mise à jour de la base de données a été effetuée !');
                return $this->redirect('/admin');
            }else{
                $this->session->setFlash('danger', "<strong>Erreur !</strong> La requête demandée s'est arrêtée !");
            }
        }
        $form = new SpectreForm($_POST);
        $this->render('dashboard/update/model.twig', [
            'form' => $form,
            'current_menu' => $this->current_menu
        ]);
    }

    /**
     * Permet de sauvegarder la base de données de Proaxive
     */
    public function mysqldumpData(){
        // Charge le fichier Json d'accès à la base de données
        $viewJsonFile = @file_get_contents(ROOT. 'src/Database/db.json');
        $viewJson = json_decode($viewJsonFile, false);
        if($viewJson == null){
            // Récupère les informations de connexion à la base de données via le fichier de configuration
            $host = getenv('DB_HOST');
            $database = getenv('DB_DATABASE');
            $user = getenv('DB_USERNAME');
            $password = getenv('DB_PASSWORD');
        } else {
            // Initialise les variables avec les données du Json
            $host = $viewJson->db_host;
            $database = $viewJson->db_name;
            $user = $viewJson->db_user;
            $password = $viewJson->db_passwd;
        }
        // Charge Mysqldump
        $dumper = new IMysqldump\Mysqldump('mysql:host='.$host.';dbname='. $database, $user, $password);
        // Envoi le fichier .sql vers le dossier de sauvegarde
        $dumper->start(ROOT.'db/save/dump-'.time().'.sql');
        if($dumper){
            $this->session->setFlash('success', 'La base de données a été sauvegardée !');
            $this->updateDatabase();
        }
    }

    /**
     *
     */
    private function readJson(){
        $viewJsonFile = file_get_contents(ROOT . '/src/Database/db.json');
        return json_decode($viewJsonFile, false);
    }
}