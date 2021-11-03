<?php
/*
 * AdminAddInterventionController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date October 20, 2021
 */
namespace App\Controller\Dashboard;

use App;
use Creitive\Breadcrumbs\Breadcrumbs;
use src\Auth\Auth;
use src\Form\SpectreForm;
use src\MyClass\Session;
use src\MyClass\Str;

class AdminAddInterventionController extends AppController
{
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

    }

    public function add(){
        $generate = new Str();
        if(!empty($_POST)){
            $client = $this->Client->find('id', $_POST['client_id']);
            if($client->sleeping == 1){
                $this->session->setFlash('danger', "Le client est en sommeil, veuillez le réveiller avant d'effectuer cette action.");
                return $this->redirect('/admin/edit-client/' .$client->id);
            }
            if(!empty($_POST['equipment_id'])){
                if(empty($_POST['pmad'])){$_POST['pmad'] = 0;}
                $number = $_POST['number'];
                $received = $_POST['received'];
                $scanNumber = $this->Intervention->scanNumber($number);
                // Vérifie si le numéro de l'intervention existe ou non
                if ($scanNumber) {
                    $this->session->setFlash('danger', 'Ce numéro existe déjà !');

                } else {
                    // Si un numéro n'est pas rentré, on en génère un
                    if(empty($number)){
                        $number = rand(8, 9999999);
                    }
                    // Vérifie si le champ "received est renseigné
                    if(empty($received)){
                        $received = NULL;
                    }
                    // Si tout est OK, on envoi la requete
                    $result = $this->Intervention->create([
                        'status_id' => $_POST['status_id'],
                        'received' => $received,
                        'details' => $_POST['details'],
                        'description' => $_POST['description'],
                        'report' => null,
                        'observation' => $_POST['observation'],
                        'note_tech' => $_POST['note_tech'],
                        'title' => $_POST['title'],
                        'inter_type' => $_POST['inter_type'],
                        'steps' => $_POST['steps'],
                        'number' => $number,
                        'price' => 0,
                        'nb_views' => 0,
                        'number_link' => $generate->random(10),
                        'client_id' => $_POST['client_id'],
                        'closed' => null,
                        'pmad' => $_POST['pmad'],
                        'equipment_id' => $_POST['equipment_id'],
                        'author_name' => $this->session->read('auth')->fullname,
                        'auser_id' => $this->auth->getUserId(),
                        'created_at' => $this->newDate,
                        'updated_at' => $this->newDate
                    ]);
                    $lastInserId = $this->Intervention->lastInsertId();
                    $intervention = $this->Intervention->find('id', $lastInserId);
                    if($_POST['steps'] == 2){
                        $this->Equipment->update(
                            'id', (int) $intervention->equipment_id,
                            [
                                'inworkshop' => 1
                            ]);
                    }
                    if($result){
                        $this->session->setFlash('success', 'La nouvelle intervention a bien été créée !');
                        return $this->redirect('/admin/intervention/'.$intervention->id);
                    }
                }
            } else {
                $this->session->setFlash('danger', 'Vous devez choisir un équipement et/ou renseigner une date de dépôt !');
            }
        } // Fin du IF $_POST

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Interventions', '/admin/interventions')
            ->addCrumb('Nouvelle intervention', '');
        $this->breadcrumbs->render();

        $client = $this->Client->extractByAlpha('id', 'fullname');
        $computer = $this->Equipment->extract('id', 'name');
        $status = $this->Status->extract('id', 'title');
        $countEquipment = $this->Equipment->countRow();
        $form = new SpectreForm($_POST);
        $this->render('intervention/admin/add.twig', [
            'client' => $client,
            'computer' => $computer,
            'status' => $status,
            'countEquipment' => $countEquipment,
            'form' => $form,
            'breadcrumbs' => $this->breadcrumbs,
            'current_menu' => $this->current_menu
        ]);


    }

    /**
     * Permet d'ajouter une intervention avec client selectionné
     * @return type
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    public function addWithClient(int $id){
        $auth = new Auth(app::getInstance()->getDB());
        $generate = new Str();
        $client = $this->Client->find('id', (int) $id);
        $computer = $this->Equipment->extractById('client_id', 'name', (int) $id);

        if(empty($computer)){
            $this->session->setFlash('danger', 'Ce client n\'a aucun équipement !');
            $this->redirect('/admin/client/'.$client->id.'/add-equipment');
        }

        if(!empty($_POST)){
            if(!empty($_POST['title'])){
                if(empty($_POST['pmad'])){$_POST['pmad'] = 0;}
                $number = $_POST['number'];
                $received = $_POST['received'];
                $scanNumber = $this->Intervention->scanNumber($number);
                // Vérifie si le numéro de l'intervention existe ou non
                if ($scanNumber) {
                    $this->session->setFlash('danger', 'Ce numéro existe déjà !');

                } else {
                    // Si un numéro n'est pas rentré, on en génère un
                    if(empty($number)){
                        $number = rand(8, 9999999);
                    }
                    // Vérifie si le champ "received est renseigné
                    if(empty($received)){
                        $received = NULL;
                    }
                    // Si tout est OK, on envoi la requete
                    $result = $this->Intervention->create([
                        'status_id' => $_POST['status_id'],
                        'received' => $_POST['received'],
                        'details' => $_POST['details'],
                        'description' => $_POST['description'],
                        'report' => null,
                        'observation' => $_POST['observation'],
                        'note_tech' => $_POST['note_tech'],
                        'inter_type' => $_POST['inter_type'],
                        'title' => $_POST['title'],
                        'steps' => $_POST['steps'],
                        'number' => $number,
                        'price' => 0,
                        'number_link' => $generate->random(10),
                        'client_id' => (int) $id,
                        'closed' => null,
                        'pmad' => $_POST['pmad'],
                        'nb_views' => 0,
                        'author_name' => $this->session->read('auth')->fullname,
                        'equipment_id' => $_POST['equipment_id'],
                        'auser_id' => $this->auth->getUserId(),
                        'created_at' => $this->newDate,
                        'updated_at' => $this->newDate
                    ]);
                    $lastInserId = $this->Intervention->lastInsertId();
                    $intervention = $this->Intervention->find('id', $lastInserId);
                    if($result){
                        $this->session->setFlash('success', 'La nouvelle intervention pour <strong>' . $client->fullname . '</strong> a bien été créée !');
                        return $this->redirect('/admin/intervention/'.$intervention->id);
                    }
                }
            } else {
                $this->session->setFlash('danger', 'Vous devez entrer un titre pour votre intervention et renseigner une date de dépôt !');
            }
        } // Fin du IF $_POST

        if($client->sleeping == 1){
            $this->session->setFlash('danger', "Le client est en sommeil, veuillez le réveiller avant d'effectuer cette action.");
            return $this->redirect('/admin/edit-client/' .$client->id);
        }

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Interventions', '/admin/interventions')
            ->addCrumb($client->fullname, '/admin/client/'.$client->id)
            ->addCrumb('Création', '');
        $this->breadcrumbs->render();

        $computer = $this->Equipment->extractById('client_id', 'name', (int) $id);
        $status = $this->Status->extract('id', 'title');
        $countEquipment = $this->Equipment->countRow();
        $form = new SpectreForm($_POST);
        $this->render('intervention/admin/addWithClient.twig', [
            'client' => $client,
            'computer' => $computer,
            'status' => $status,
            'countEquipment' => $countEquipment,
            'form' => $form,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);


    }

    /**
     * Permet d'ajouter une intervention avec client selectionné
     * @param int $id
     * @param int $equipment_id
     * @return type
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    public function addWithClientAndEquipment(int $client_id, int $equipment_id){
        $auth = new Auth(app::getInstance()->getDB());
        $generate = new Str();
        $client = $this->Client->find('id', (int) $client_id);
        $equipment = $this->Equipment->find('id', (int) $equipment_id);
        $categoryEquipment = $this->CategoryEquipment->find('id', $equipment->category_equipment_id);
        $brand = $this->Brand->find('id', $equipment->brand_id);
        if(!empty($_POST)){
            if(!empty($_POST['title'])){
                if(empty($_POST['pmad'])){$_POST['pmad'] = 0;}else{$_POST['received'] = null;$_POST['steps'] = null;}
                $number = $_POST['number'];
                $received = $_POST['received'];
                $scanNumber = $this->Intervention->scanNumber($number);
                // Vérifie si le numéro de l'intervention existe ou non
                if ($scanNumber) {
                    $this->session->setFlash('danger', 'Ce numéro existe déjà !');

                } else {
                    // Si un numéro n'est pas rentré, on en génère un
                    if(empty($number)){
                        $number = rand(8, 9999999);
                    }
                    // Vérifie si le champ "received est renseigné
                    if(empty($received)){
                        $received = NULL;
                    }
                    // Si tout est OK, on envoi la requete
                    $result = $this->Intervention->create([
                        'status_id' => $_POST['status_id'],
                        'received' => $_POST['received'],
                        'details' => $_POST['details'],
                        'description' => $_POST['description'],
                        'report' => null,
                        'observation' => $_POST['observation'],
                        'note_tech' => $_POST['note_tech'],
                        'inter_type' => $_POST['inter_type'],
                        'title' => $_POST['title'],
                        'steps' => $_POST['steps'],
                        'number' => $number,
                        'price' => 0,
                        'number_link' => $generate->random(10),
                        'client_id' => (int) $client_id,
                        'closed' => null,
                        'pmad' => $_POST['pmad'],
                        'nb_views' => 0,
                        'author_name' => $this->session->read('auth')->fullname,
                        'equipment_id' => (int) $equipment_id,
                        'auser_id' => $this->auth->getUserId(),
                        'created_at' => $this->newDate,
                        'updated_at' => $this->newDate
                    ]);
                    // Récupère le dernier ID de l'interventio ajoutée
                    $lastInserId = $this->Intervention->lastInsertId();
                    $intervention = $this->Intervention->find('id', $lastInserId);
                    // Si l'équipement est selectionné en "En atelier" on le met à jour.
                    if($_POST['steps'] == 2){
                        $this->Equipment->update(
                            'id', (int) $intervention->equipment_id,
                            [
                                'inworkshop' => 1
                            ]);
                    }
                    if($result){
                        $this->session->setFlash('success', 'La nouvelle intervention pour <strong>' . $client->fullname . '</strong> a bien été créée !');
                        return $this->home();
                    }
                }
            } else {
                $this->session->setFlash('danger', 'Vous devez entrer un titre pour votre intervention et renseigner une date de dépôt !');
            }
        } // Fin du IF $_POST

        if($client->sleeping == 1){
            $this->session->setFlash('danger', "Le client est en sommeil, veuillez le réveiller avant d'effectuer cette action.");
            return $this->redirect('/admin/edit-client/' .$client->id);
        }

        $status = $this->Status->extract('id', 'title');
        $countEquipment = $this->Equipment->countRow();

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Interventions', '/admin/interventions')
            ->addCrumb($client->fullname, '/admin/client/'.$client->id)
            ->addCrumb($equipment->name,'/admin/edit-equipment/'.$equipment->id)
            ->addCrumb('Création', '');
        $this->breadcrumbs->render();

        $form = new SpectreForm($_POST);
        $this->render('intervention/admin/addWithClientAndEquipement.twig', [
            'client' => $client,
            'equipment' => $equipment,
            'categoryEquipment' => $categoryEquipment,
            'brand' => $brand,
            'status' => $status,
            'form' => $form,
            'countEquipment' => $countEquipment,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }


}