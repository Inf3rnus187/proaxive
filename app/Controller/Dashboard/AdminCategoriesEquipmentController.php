<?php
/*
 * AdminCategoriesEquipmentController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */

namespace App\Controller\Dashboard;


use App;
use Creitive\Breadcrumbs\Breadcrumbs;
use src\Form\SpectreForm;
use src\MyClass\Session;

class AdminCategoriesEquipmentController extends AppController
{
    private $current_menu;

    /**
     * @var Breadcrumbs
     */
    private Breadcrumbs $breadcrumbs;

    /**
     * @var Session
     */
    private Session $session;

    public function __construct(){
        $this->current_menu = 'equipments';
        $this->breadcrumbs = new Breadcrumbs();
        $this->session = Session::getSessionInstance();
        parent::__construct();
        // Charge les tables de la base de données
        $this->loadModel('CategoryEquipment');
    }

    /**
     * Permet de lister et d'ajouter un statut
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function home(){
        if(!empty($_POST['title']) && !empty($_POST['color'])){
            if(isset($_POST['new-category']))
            {
                $result = $this->CategoryEquipment->create([
                    'title' => $_POST['title'],
                    'color' => $_POST['color'],
                    'slug' => $_POST['slug'],
                    'icon' => $_POST['icon']
                ]);

                if($result){
                    $this->session->setFlash('success', 'La catégorie <strong>'.$_POST['title'].'</strong> a bien été ajoutée !');
                }
            }
        }
        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Equipements', '/admin/equipments')
            ->addCrumb('Catégories', '');
        $this->breadcrumbs->render();

        $categories = $this->CategoryEquipment->all();
        $form = new SpectreForm($_POST);
        $this->render('equipments/admin/categories/home.twig', [
            'form' => $form,
            'categories' => $categories,
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
            if (isset($_POST['title'])){
                $result = $this->CategoryEquipment->update(
                    'id', $id,
                    [
                        'title' => $_POST['title'],
                        'color' => $_POST['color'],
                        'slug' => $_POST['slug'],
                        'icon' => $_POST['icon']
                    ]);
                if($result){
                    $this->session->setFlash('success', 'Les données ont bien été modifiées');
                    return $this->redirect('/admin/equipments/categories');
                }
            }
        }
    }


    /**
     * Permet de supprimer un statut
     *
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function delete(){
        if(!empty($_POST)){
            $this->CategoryEquipment->delete('id', $_POST['id']);
            $this->session->setFlash('success', 'La catégorie a été supprimée de la base de données');
            return $this->redirect('/admin/equipments/categories');
        }

    }
}