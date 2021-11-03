<?php
/*
 * AdminSocietyController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date October 22, 2021
 */
namespace App\Controller\Dashboard;

use Creitive\Breadcrumbs\Breadcrumbs;
use src\Form\SpectreForm;
use src\MyClass\CSV;
use src\MyClass\Session;

class AdminSocietyController extends AppController
{

    private $current_menu;

    /**
     * @var session
     */
    private $session;

    /**
     * @var Breadcrumbs
     */
    private Breadcrumbs $breadcrumbs;

    public function __construct(){
        $this->current_menu = 'society';
        $this->session = Session::getSessionInstance();
        $this->breadcrumbs = new Breadcrumbs();
        parent::__construct();
        // Charge les tables de la base de données
        $this->loadModel('Society');
    }

    /**
     * Permet de lister les sociétés
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function home(){
        $society = $this->Society->all();

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Société', "society");
        $this->breadcrumbs->render();

        $this->render('society/admin/home.twig', [
            'society' => $society,
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
            // Si tout est OK, on envoi la requete
            $result = $this->Society->create([
                's_name' => $_POST['s_name'],
                'about' => $_POST['about'],
                'tva_number' => $_POST['tva_number'],
                'siret_number' => $_POST['siret_number'],
                'naf_number' => $_POST['naf_number'],
                'address' => $_POST['address'],
                'postal_code' => $_POST['postal_code'],
                'city' => $_POST['city'],
                'department' => $_POST['department'],
                'phone' => $_POST['phone'],
                'phone_2' => $_POST['phone_2'],
                'website' => $_POST['website'],
                's_mail' => $_POST['s_mail'],
                'created_at' => $date,
                'updated_at' => $date
            ]);
            if($result){
                $this->session->setFlash('success', 'La société a bien été créée !');
                return $this->home();
            }
        } // Fin du IF $_POST

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Débours', "/admin/outlay")
            ->addCrumb('Création', "debours");
        $this->breadcrumbs->render();

        $form = new SpectreForm($_POST);
        $this->render('society/admin/add.twig', [
            'form' => $form,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    /**
     * Permet d'éditer un statut
     *
     * @render
     * @param int $id
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    public function edit(int $id){

        if(!empty($_POST['s_name'])){
            $result = $this->Society->update(
                'id', $id,
                [
                    's_name' => $_POST['s_name'],
                    'about' => $_POST['about'],
                    'tva_number' => $_POST['tva_number'],
                    'siret_number' => $_POST['siret_number'],
                    'naf_number' => $_POST['naf_number'],
                    'address' => $_POST['address'],
                    'postal_code' => $_POST['postal_code'],
                    'city' => $_POST['city'],
                    'department' => $_POST['department'],
                    'phone' => $_POST['phone'],
                    'phone_2' => $_POST['phone_2'],
                    'website' => $_POST['website'],
                    's_mail' => $_POST['s_mail'],
                ]);
            if($result){
                $this->session->setFlash('success', 'Les données ont bien été modifiées');
            }
        }

        $society = $this->Society->find('id', $id);

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Society', '/admin/society')
            ->addCrumb($society->s_name, '');
        $this->breadcrumbs->render();

        $form = new SpectreForm($society);
        $this->render('society/admin/edit.twig', [
            'society' => $society,
            'form' => $form,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    /**
     * Permet d'exporter les données au format CSV
     */
    public function exportSociety(){
        $clients = $this->Society->exportSociety();
        if(!empty($_POST)){
            $exportData = new CSV();
            $exportData->export_thead($clients, 'Export-Society');
        }
    }

    /**
     * Permet de supprimer un statut
     *
     * @return void
     */
    public function delete(){
        if(!empty($_POST)){
            $this->Society->delete('id', $_POST['id']);
            $this->session->setFlash('success', 'La société a été supprimée de la base de données');
            return $this->redirect('/admin/society');
        }

    }
}