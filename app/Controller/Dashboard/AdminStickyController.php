<?php
/*
 * AdminStickyController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date October 19, 2021
 */

namespace App\Controller\Dashboard;

use App;
use src\Auth\Auth;
use src\Form\Form;
use src\Form\SpectreForm;
use src\MyClass\Session;
use Creitive\Breadcrumbs\Breadcrumbs;
use src\MyClass\Str;

class AdminStickyController extends AppController
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
        $this->current_menu = 'sticky';
        $this->breadcrumbs = $breadcrumbs = new Breadcrumbs();
        $this->session = Session::getSessionInstance();
        $this->auth = new Auth(app::getInstance()->getDB());
        // Charge les tables de la base de données
        $this->loadModel('Sticky');
    }

    /**
     * Permet de lister les utilisateurs/clients
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index(){
        $stickies = $this->Sticky->all();
        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Notes', "stickies");
        $this->breadcrumbs->render();

        $form = new SpectreForm($_POST);
        $this->render('sticky/admin/home.twig', [
            'stickies' => $stickies,
            'form' => $form,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);

    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function add()
    {
        $date = date('Y-m-d H:i:s');
        $str = new Str();
        if(!empty($_POST)){
        if(empty($_POST['stick'])){$_POST['stick'] = 0;}
            if(!empty($_POST['content'])){
                if ($str->limitCharacter($_POST['content'], 180) == false) {
                    $this->session->setFlash('danger', 'Vous avez dépassé le nombre maximum de caractères autorisés !');
                } else {
                    // Si tout est OK, on envoi la requete
                    $result = $this->Sticky->create([
                        'title' => $_POST['title'],
                        'content' => $_POST['content'],
                        'bgcolor' => $_POST['bgcolor'],
                        'txtcolor' => $_POST['txtcolor'],
                        'stick' => $_POST['stick'],
                        'auser_id' => $this->auth->getUserId(),
                        'created_at' => $date,
                        'updated_at' => $date
                    ]);
                    if($result){
                        $this->session->setFlash('success', 'La note a bien été créée !');
                        return $this->index();
                    }
                }
            } else {
                $this->session->setFlash('danger', 'Votre note ne contient aucun contenu !');
            }
    } // Fin du IF $_POST

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Notes', "sticky")
            ->addCrumb('Création', false);
        $this->breadcrumbs->render();

        $form = new SpectreForm($_POST);
        $this->render('sticky/admin/add.twig', [
            'form' => $form,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    /**
     * Permet d'éditer une note
     *
     * @render
     * @param int $id
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    public function edit(int $id){
        $str = new Str();
        if(!empty($_POST)){
            if(isset($_POST['update'])){
                if(!empty($_POST['title'])){
                    if(empty($_POST['stick'])){$_POST['stick'] = 0;}
                    if($str->limitCharacter($_POST['content'], 180) == false){
                        $this->session->setFlash('danger', 'Vous avez dépassé le nombre maximum de caractères autorisés !');
                    } else {
                        $result = $this->Sticky->update(
                            'id', $id,
                            [
                                'title' => $_POST['title'],
                                'content' => $_POST['content'],
                                'stick' => $_POST['stick'],
                                'bgcolor' => $_POST['bgcolor'],
                                'txtcolor' => $_POST['txtcolor'],
                            ]);
                        if($result){
                            $this->session->setFlash('success', 'Les données ont bien été modifiées');
                            return $this->index();
                        }
                    }
                }
            }
            if(isset($_POST['delete'])){
                $this->delete($id);
            }
        }

        $sticky = $this->Sticky->find('id', $id);

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Sticky', '/admin/sticky')
            ->addCrumb($sticky->title, '');
        $this->breadcrumbs->render();

        $form = new SpectreForm($sticky);
        $this->render('sticky/admin/edit.twig', [
            'sticky' => $sticky,
            'form' => $form,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }


    /**
     * Permet de supprimer une note
     *
     * @return void
     */
    public function delete($id){
        $sticky = $this->Sticky->find('id', $id);
        if($sticky->auser_id != $this->auth->getUserId()){
            App::getAuth()->isAdminOnly();
        }
        $this->Sticky->delete('id', $id);
        $this->session->setFlash('success', 'La note a été supprimée de la base de données');
        return $this->redirect('/admin/sticky');
    }

    /**
     * Permet de supprimer toutes les notes
     */
    public function deleteAll(){
        App::getAuth()->isAdminOnly();
        if(!empty($_POST)){
            if(isset($_POST['delete'])){
                $this->Sticky->allDelete();
                $this->session->setFlash('success', 'Toutes les notes ont été supprimées de la base de données.');
                return $this->redirect('/admin/sticky');
            }
        }
        return $this->redirect('/');
    }
}