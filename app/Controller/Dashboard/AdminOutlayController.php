<?php
/*
 * AdminOutlayController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date Jun 16, 2021
 */

namespace App\Controller\Dashboard;

use App;
use src\Auth\Auth;
use src\Form\Form;
use src\Form\SpectreForm;
use src\MyClass\CSV;
use src\MyClass\Session;
use Creitive\Breadcrumbs\Breadcrumbs;

class AdminOutlayController extends AppController
{
    /**
     * @var string
     */
    private $current_menu;

    private Breadcrumbs $breadcrumbs;
    private Session $session;
    /**
     * @var Auth
     */
    private Auth $auth;

    public function __construct(){

        parent::__construct();
        $this->current_menu = 'outlay';
        $this->breadcrumbs = new Breadcrumbs();
        $this->session = Session::getSessionInstance();
        $this->auth = new Auth(app::getInstance()->getDB());
        // Charge les tables de la base de données
        $this->loadModel('Client');
        $this->loadModel('Status');
        $this->loadModel('User');
        $this->loadModel('Outlay');
        $this->loadModel('Company');
        $this->loadModel('Intervention');
    }

    /**
     * Permet de lister les utilisateurs/clients
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function home(){
        $outlay = $this->Outlay->allWithStatusAndClient();

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Débours', "débours");
        $this->breadcrumbs->render();

        $this->render('outlay/admin/home.twig', [
            'outlay' => $outlay,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    /**
     * Add outlay
     * @return type
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */

    public function add(){
        $date = date('Y-m-d H:i:s');
        $number = rand(8, 9999999);
        if(!empty($_POST)){
            if(empty($_POST['signature'])){$_POST['signature'] = 0;}
            // Si tout est OK, on envoi la requete
            $result = $this->Outlay->create([
                'number' => $number,
                'amount' => $_POST['amount'],
                'refund' => null,
                'payment' => $_POST['payment'],
                'ref_propal' => $_POST['ref_propal'],
                'signature' => $_POST['signature'],
                'seller' => $_POST['seller'],
                'toclosed' => null,
                'client_id' => $_POST['client_id'],
                'status_id' => $_POST['status_id'],
                'auser_id' => $this->auth->getUserId(),
                'created_at' => $date,
                'updated_at' => $date
            ]);
            if($result){
                $this->session->setFlash('success', 'Le débours a bien été créé !');
                return $this->home();
            }
        } // Fin du IF $_POST

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Débours', "/admin/outlay")
            ->addCrumb('Création', "debours");
        $this->breadcrumbs->render();

        $client = $this->Client->extractByAlpha('id', 'fullname');
        $status = $this->Status->extract('id', 'title');
        $form = new SpectreForm($_POST);
        $this->render('outlay/admin/add.twig', [
            'client' => $client,
            'status' => $status,
            'form' => $form,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    /**
     * Add outlay with client
     * @return type
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */

    public function addWithclient(int $id){
        $client = $this->Client->find('id', (int) $id);

        if($client == null){
            $this->session->setFlash('danger', "Le client n'exsite pas !");
            return $this->index();
        }

        $date = date('Y-m-d H:i:s');
        $auth = new Auth(app::getInstance()->getDB());
        $number = rand(8, 9999999);
        if(!empty($_POST)){
            if(empty($_POST['signature'])){$_POST['signature'] = 0;}
            // Si tout est OK, on envoi la requete
            $result = $this->Outlay->create([
                'number' => $number,
                'amount' => $_POST['amount'],
                'refund' => null,
                'payment' => $_POST['payment'],
                'ref_propal' => $_POST['ref_propal'],
                'signature' => $_POST['signature'],
                'seller' => $_POST['seller'],
                'toclosed' => null,
                'client_id' => $client->id,
                'status_id' => $_POST['status_id'],
                'auser_id' => $this->auth->getUserId(),
                'created_at' => $date,
                'updated_at' => $date
            ]);
            if($result){
                $this->session->setFlash('success', 'Le débours a bien été créé !');
                return $this->home();
            }
        } // Fin du IF $_POST

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Accueil', "/admin")
            ->addCrumb('Clients', "/admin/clients")
            ->addCrumb($client->fullname, "/admin/client/" .$client->id)
            ->addCrumb('Débours', "/admin/outlay")
            ->addCrumb('Création', "debours");
        $this->breadcrumbs->render();

        $status = $this->Status->extract('id', 'title');
        $clientIntervention = $this->Intervention->extractByIdAndKey('client_id', 'number', $client->id);
        $form = new SpectreForm($_POST);
        $this->render('outlay/admin/addWithClient.twig', [
            'client' => $client,
            'status' => $status,
            'form' => $form,
            'clientIntervention' => $clientIntervention,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    /**
     * Edit and close outlay
     * @param int $id
     * @return type
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */

    public function edit(int $id){
        $date = date('Y-m-d H:i:s');
        $outlay = $this->Outlay->find('id', (int) $id);

        // Vérifie les permissions utilisateur
        if($outlay->auser_id != $this->auth->getUserId() AND $this->session->read('auth')->roles_id != 1){
            $this->session->setFlash('danger', "[Auth_Perms] Vous ne pouvez pas modifier ce débours !");
            return $this->home();
        }

        if (!empty($_POST)) {
            if(empty($_POST['signature'])){$_POST['signature'] = 0;}
            if(isset($_POST['update'])){
                $result = $this->Outlay->update(
                    'id', (int) $id,
                    [
                        'amount' => $_POST['amount'],
                        'refund' => null,
                        'payment' => $_POST['payment'],
                        'ref_propal' => $_POST['ref_propal'],
                        'signature' => $_POST['signature'],
                        'seller' => $_POST['seller'],
                        'toclosed' => null,
                        'client_id' => $_POST['client_id'],
                        'status_id' => $_POST['status_id'],
                    ]);
                if($result){
                    $this->session->setFlash('success', "Les données ont bien été modifiées !");
                    return $this->home();
                }
            }

            if (isset($_POST['cloture'])) {
                $result = $this->Outlay->update(
                    'id', (int) $id,
                    [
                        'refund' => $date,
                        'toclosed' => 1,
                        'status_id' => 8,
                    ]);
                if($result){
                    $this->session->setFlash('success', "Le débours a bien été cloturé (réglé par le client) !");
                    $this->home();
                }
            }
        }

        //$client = $this->Client->find('id', $outlay->client_id);
        $client = $this->Client->extractByAlpha('id', 'fullname');
        $status = $this->Status->extract('id', 'title');
        $user = $this->User->find('id', $outlay->auser_id);
        $form = new SpectreForm($outlay);

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Débours', "/admin/outlay")
            ->addCrumb($outlay->number, "debours");
        $this->breadcrumbs->render();

        $this->render('outlay/admin/edit.twig', [
            'client' => $client,
            'outlay' => $outlay,
            'status' => $status,
            'form' => $form,
            'user' => $user,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);

    }

    /**
     * @param int $id
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showHtml(int $id)
    {
        $company = $this->Company->isDefault();
        $outlay = $this->Outlay->find('id', $id);
        $client = $this->Client->find('id', $outlay->client_id);
        $status = $this->Status->find('id', $outlay->status_id);

        $this->render('outlay/admin/html.twig', [
            'outlay' => $outlay,
            'client' => $client,
            'status' => $status,
            'company' => $company,
            'current_menu' => $this->current_menu
        ]);
    }

    /**
     * Permet d'exporter les données au format CSV
     */
    public function exportOutlay(){
        $outlay = $this->Outlay->exportWithClientAndStatus();
        if(!empty($_POST)){
            $exportData = new CSV();
            $exportData->export_thead($outlay, 'Export-Outlay');
        }
    }
}