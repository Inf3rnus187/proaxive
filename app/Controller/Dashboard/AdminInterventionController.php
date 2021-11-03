<?php
/*
 * AdminInterventionController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace App\Controller\Dashboard;

use \App;
use Creitive\Breadcrumbs\Breadcrumbs;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Snappy\Pdf;
use src\Entity\Entity;
use src\MyClass\Paginator;
use src\MyClass\Str;
use src\Auth\Auth;
use src\Form\SpectreForm;
use src\MyClass\SendMail;
use src\MyClass\Session;
use Verot\Upload\Upload;

/**
 * Description of WorkshopController
 *
 * @author SelMaK
 */
class AdminInterventionController extends AppController{


    private $current_menu;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var false|string
     */
    private $newDate;

    /**
     * @var Breadcrumbs
     */
    private Breadcrumbs $breadcrumbs;

    private Auth $auth;

    public function __construct(){

        parent::__construct();
        $this->current_menu = 'intervention';
        $this->session = Session::getSessionInstance();
        $this->auth = new Auth(app::getInstance()->getDB());
        $this->newDate = date('Y-m-d H:i:s');
        $this->breadcrumbs = new Breadcrumbs();
        // Charge les différentes tables de la base de données
        $this->loadModel('User');
        $this->loadModel('Client');
        $this->loadModel('Status');
        $this->loadModel('Brand');
        $this->loadModel('OperatingSystem');
        $this->loadModel('Equipment');
        $this->loadModel('CategoryEquipment');
        $this->loadModel('Company');
        $this->loadModel('Intervention');
        $this->loadModel('Society');

    }

    /**
     * Permet d'afficher la liste des interventions
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function home(){

        $paginator = new Paginator('15', 'p');
        $paginator->set_total($this->Intervention->countIntervention());
        $interventions = $this->Intervention->allWithPaginator($paginator->get_limit());
        $dataPaginator = $paginator->page_links();

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Interventions', '');
        $this->breadcrumbs->render();

        $this->render('intervention/admin/home.twig', [
            'interventions' => $interventions,
            'dataPaginator' => $dataPaginator,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);

    }

    /**
     * Permet d'afficher le rapport BAO + Client
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function bao(int $id){
        $intervention = $this->Intervention->find('id', $id);
        $client = $this->Client->find('id', $intervention->client_id);
        $equipment = $this->Equipment->find('id', $intervention->equipment_id);
        $clientFullName = Entity::cleanText($client->fullname);
        if(!$intervention->rapport_bao){
            $this->session->setFlash('danger', "Il n'y a aucun rapport BAO pour cette intervention !");
            return $this->home();
        }
        $file = ROOT . "documents/clients/$clientFullName/bao/" .$intervention->rapport_bao;
        $rapport = $this->openFile($file, 'r');
        if(!empty($_POST)){
            if(isset($_POST['rapport'])){
                $rapportBao = $_POST['rapport_bao'];
                // Vérifie si le fichier peut être écrit.
                if (is_writable($file)) {
                    if (!$handle = fopen($file, 'w+')) {
                        $this->session->setFlash('danger', "Impossible d'ouvrir le fichier ($file)");
                        exit;
                    }
                    if (fwrite($handle, $rapportBao) === FALSE) {
                        echo "Impossible d'écrire dans le fichier ($file)";
                        exit;
                    }
                    $this->session->setFlash('success', "<strong>[Rafraîchissement dans 3s]</strong> L'écriture du rapport <strong>$intervention->rapport_bao</strong> dans le fichier ($file) a réussi");
                    fclose($handle);
                    header("Refresh: 3;url=/admin/intervention/$intervention->id/rapport/bao");


                } else {
                    $this->session->setFlash('danger', "Le fichier $file n'est pas accessible en écriture.");

                }
            }

            if(isset($_POST['delete_file'])){
                $this->deleteFile($file);
                $this->Intervention->update(
                    'id', (int) $id,
                    [
                        'rapport_bao' => '',
                    ]
                );
                $this->session->setFlash('success', "Le fichier <strong>$intervention->rapport_bao</strong> a été supprimé.");
                return $this->home();
            }

        }

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Interventions', '/admin/interventions')
            ->addCrumb($client->fullname, '/admin/client/'.$client->id)
            ->addCrumb($equipment->name,'/admin/edit-equipment/'.$equipment->id)
            ->addCrumb($intervention->title, '/admin/intervention/'.$intervention->id)
            ->addCrumb('Rapport BAO', '');
        $this->breadcrumbs->render();

        $this->render('intervention/admin/bao.twig', [
            'intervention' => $intervention,
            'rapport' => $rapport,
            'client' => $client,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);

    }

    /**
     * Permet d'afficher la vue HTML d'une intervention
     * @param int $id
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function showHtml(int $id)
    {
        $intervention = $this->Intervention->findByKeyAndValue('id', (int) $id);
        $client = $this->Client->find('id', $intervention->client_id);
        $user = $this->User->find('id', $intervention->auser_id);
        $equipment = $this->Equipment->find('id', $intervention->equipment_id);
        $brand = $this->Brand->find('id', $equipment->brand_id);
        $operatingSystem = $this->OperatingSystem->find('id', $equipment->operating_systems_id);
        $categoryEquipment = $this->CategoryEquipment->find('id', $equipment->category_equipment_id);
        $society = $this->Society->find('id', $client->society_id);
        $company = $this->Company->isDefault();

        $this->render('intervention/admin/html.twig', [
            'current_menu' => $this->current_menu,
            'intervention' => $intervention,
            'client' => $client,
            'user' => $user,
            'company' => $company,
            'equipment' => $equipment,
            'brand' => $brand,
            'society' => $society,
            'operatingSystem' => $operatingSystem,
            'categoryEquipment' => $categoryEquipment

        ]);
    }

    /**
     * Permet de supprimer une intervention
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function delete(){
        App::getAuth()->isAdminOnly();
        if(!empty($_POST)){
            if (isset($_POST)) {
                $this->Intervention->delete('id', (int) $_POST['id']);
                $this->session->setFlash('success', "L'intervention a été supprimée.");
                return $this->home();
            } else {
                $this->session->setFlash('danger', "L'intervention'n'a pas pu être supprimée.");
                return $this->home();
            }
        }
    }

}
