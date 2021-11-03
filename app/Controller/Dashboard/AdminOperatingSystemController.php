<?php
/*
 * AdminOperatingSystemController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */

namespace App\Controller\Dashboard;
use Creitive\Breadcrumbs\Breadcrumbs;
use src\Form\SpectreForm;
use src\MyClass\Session;

class AdminOperatingSystemController extends AppController
{
    /**
     * @var string
     */
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

        parent::__construct();
        $this->current_menu = 'equipments';
        $this->session = Session::getSessionInstance();
        $this->breadcrumbs = new Breadcrumbs();
        // Charge les tables de la base de données
        $this->loadModel('OperatingSystem');
    }

    /**
     * Permet de lister et d'ajouter un OS
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function home(){
        if(!empty($_POST['os_name'])){
            if(isset($_POST['new-os'])){
                $os_full = $_POST['os_name'].'-'.$_POST['os_release'].'-'.$_POST['os_architecture'];
                $result = $this->OperatingSystem->create([
                    'os_name' => $_POST['os_name'],
                    'os_release' => $_POST['os_release'],
                    'os_architecture' => $_POST['os_architecture'],
                    'os_full' => $os_full
                ]);
                if($result){
                    $this->session->setFlash('success', 'Le système d\'exploitation <strong>'.$_POST['os_name'].'</strong> a bien été ajouté !');
                }
            }
        }

        $operatingsystem = $this->OperatingSystem->all();

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Systèmes d\'exploitation', '');
        $this->breadcrumbs->render();

        $form = new SpectreForm($_POST);
        $this->render('operating-system/admin/home.twig', [
            'form' => $form,
            'operatingsystem' => $operatingsystem,
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
     */

    public function edit(int $id){
        if(!empty($_POST)){
            $os_full = $_POST['os_name'].'-'.$_POST['os_release'].'-'.$_POST['os_architecture'];
            $result = $this->OperatingSystem->update(
                'id', $id,
                [
                    'os_name' => $_POST['os_name'],
                    'os_release' => $_POST['os_release'],
                    'os_architecture' => $_POST['os_architecture'],
                    'os_full' => $os_full
                ]);
            if($result){
                $this->session->setFlash('success', 'Les données ont bien été modifiées');
                return $this->redirect('/admin/operating-systems');
            }
        }
    }


    /**
     * Permet de supprimer un système d'exploitation
     *
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function delete(){
        if(!empty($_POST)){
            $this->OperatingSystem->delete('id', $_POST['id']);
            $this->session->setFlash('success', 'Le système d\'exploitation a été supprimée de la base de données');
            return $this->redirect('/admin/operating-systems');
        }

    }
}