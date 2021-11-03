<?php
/*
 * AdminEquipmentController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */

namespace App\Controller\Dashboard;

use App;
use Creitive\Breadcrumbs\Breadcrumbs;
use src\Auth\Auth;
use src\Entity\Entity;
use src\Form\SpectreForm;
use src\MyClass\BAO;
use src\MyClass\CSV;
use src\MyClass\Session;
use Verot\Upload\Upload;


class AdminEquipmentController extends AppController
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
     * @var false|string
     */
    private $newDate;

    /**
     * @var Breadcrumbs
     */
    private Breadcrumbs $breadcrumbs;

    /**
     * @var Auth
     */
    private Auth $auth;


    public function __construct(){

        parent::__construct();
        $this->current_menu = 'equipments';
        $this->session = Session::getSessionInstance();
        $this->auth = new Auth(app::getInstance()->getDB());
        $this->breadcrumbs = new Breadcrumbs();
        $this->newDate = date('Y-m-d H:i:s');
        // Charge les tables de la base de données
        $this->loadModel('Equipment');
        $this->loadModel('User');
        $this->loadModel('CategoryEquipment');
        $this->loadModel('Brand');
        $this->loadModel('Client');
        $this->loadModel('Intervention');
        $this->loadModel('OperatingSystem');
    }

    /**
     * Permet de lister les équipements
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function home()
    {
        $equipments = $this->Equipment->allWithClientCategoryBrandsOS();

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('équipements', '');
        $this->breadcrumbs->render();

        $this->render('equipments/admin/home.twig', [
            'equipments' => $equipments,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    /**
     * Permet d'ajouter un équipement
     *
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function add()
    {
        if(!empty($_POST)){
            $client = $this->Client->find('id', $_POST['client_id']);
            if($client->sleeping == 1){
                $this->session->setFlash('danger', "Le client est en sommeil, veuillez le réveiller avant d'effectuer cette action.");
                return $this->redirect('/admin/edit-client/' .$client->id);
            }
            if(!empty($_POST['name'])){
                if(empty($_POST['inworkshop'])){$_POST['inworkshop'] = 0;}
                if(empty($_POST['status'])){$_POST['status'] = 0;}
                $result = $this->Equipment->create([
                    'name' => $_POST['name'],
                    'content' => $_POST['content'],
                    'r_install_date' => $_POST['r_install_date'],
                    'r_pc_name' => $_POST['r_pc_name'],
                    'r_bios' => $_POST['r_bios'],
                    'r_manufacturer' => $_POST['r_manufacturer'],
                    'r_ram' => $_POST['r_ram'],
                    'r_cpu' => $_POST['r_cpu'],
                    'r_socket' => $_POST['r_socket'],
                    'r_mb' => $_POST['r_mb'],
                    'r_sn' => $_POST['r_sn'],
                    'r_cg' => $_POST['r_cg'],
                    'inworkshop' => $_POST['inworkshop'],
                    'serialnumber' => $_POST['serialnumber'],
                    'numberproduct' => $_POST['numberproduct'],
                    'licence_os' => $_POST['licence_os'],
                    'status' => $_POST['status'],
                    'year' => $_POST['year'],
                    'client_id' => $_POST['client_id'],
                    'brand_id' => $_POST['brand_id'],
                    'category_equipment_id' => $_POST['category_equipment_id'],
                    'operating_systems_id' => $_POST['operating_systems_id'],
                    'created_at' => $this->newDate,
                    'auser_id' => $this->auth->getUserId(),
                    'updated_at' => $this->newDate,
                ]);
                if($result){
                    $this->session->setFlash('success', "L'équipement a bien été ajouté");
                    return $this->home();
                }
                else {
                    $this->session->setFlash('danger', "Il y a eu un problème avec la base de données");
                }
            } else {
                $this->session->setFlash('danger', "Les champs <strong>Nom de l'équipement</strong> et <strong>Détails de l'équipement</strong> sont obligatoires");
            }
        }

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('équipements', '/admin/equipments')
            ->addCrumb('création', '');
        $this->breadcrumbs->render();

        $form = new SpectreForm($_POST);
        $categories = $this->CategoryEquipment->extract('id', 'title');
        $brands = $this->Brand->extractByAlpha('id', 'b_title');
        $client = $this->Client->extractByAlpha('id', 'fullname');
        $operating = $this->OperatingSystem->extract('id', 'os_full');
        $this->render('equipments/admin/add.twig', [
            'form' => $form,
            'categories' => $categories,
            'brands' => $brands,
            'client' => $client,
            'operating' => $operating,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    /**
     * Permet d'ajouter un équipement
     *
     * @param int $id
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function addWithClient(int $id)
    {
        $client = $this->Client->find('id', (int) $id);

        if(!empty($_POST)){
            if(empty($_POST['inworkshop'])){$_POST['inworkshop'] = 0;}
            if(empty($_POST['status'])){$_POST['status'] = 0;}
            if(!empty($_POST) && !empty($_POST['name'])){
                $result = $this->Equipment->create([
                    'name' => $_POST['name'],
                    'content' => $_POST['content'],
                    'r_install_date' => $_POST['r_install_date'],
                    'r_pc_name' => $_POST['r_pc_name'],
                    'r_bios' => $_POST['r_bios'],
                    'r_manufacturer' => $_POST['r_manufacturer'],
                    'r_ram' => $_POST['r_ram'],
                    'r_cpu' => $_POST['r_cpu'],
                    'r_socket' => $_POST['r_socket'],
                    'r_mb' => $_POST['r_mb'],
                    'r_sn' => $_POST['r_sn'],
                    'r_cg' => $_POST['r_cg'],
                    'inworkshop' => $_POST['inworkshop'],
                    'serialnumber' => $_POST['serialnumber'],
                    'numberproduct' => $_POST['numberproduct'],
                    'licence_os' => $_POST['licence_os'],
                    'status' => $_POST['status'],
                    'year' => $_POST['year'],
                    'client_id' => $client->id,
                    'brand_id' => $_POST['brand_id'],
                    'auser_id' => $this->auth->getUserId(),
                    'category_equipment_id' => $_POST['category_equipment_id'],
                    'operating_systems_id' => $_POST['operating_systems_id'],
                    'created_at' => $this->newDate,
                    'updated_at' => $this->newDate,
                ]);
                if($result){
                    $this->session->setFlash('success', "L'équipement pour <strong>$client->fullname</strong> a bien été ajouté");
                    return $this->home();
                }
                else {
                    $this->session->setFlash('danger', "Il y a eu un problème avec la base de données");
                }
            } else {
                $this->session->setFlash('danger', "Les champs <strong>Nom de l'équipement</strong> et <strong>Détails de l'équipement</strong> sont obligatoires");
            }
        }

        if($client->sleeping == 1){
            $this->session->setFlash('danger', "Le client est en sommeil, veuillez le réveiller avant d'effectuer cette action.");
            return $this->redirect('/admin/edit-client/' .$client->id);
        }

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('équipements', '/admin/equipments')
            ->addCrumb($client->fullname, '/admin/client/'.$client->id)
            ->addCrumb('création', '');
        $this->breadcrumbs->render();

        $form = new SpectreForm($_POST);
        $categories = $this->CategoryEquipment->extract('id', 'title');
        $brands = $this->Brand->extractByAlpha('id', 'b_title');
        $operating = $this->OperatingSystem->extract('id', 'os_full');
        $this->render('equipments/admin/addwithclient.twig', [
            'form' => $form,
            'categories' => $categories,
            'brands' => $brands,
            'client' => $client,
            'operating' => $operating,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    /**
     * Permet d'éditer un ordinateur client
     *
     * @param int $id
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function edit(int $id){
            if(!empty($_POST)){
                if(empty($_POST['inworkshop'])){$_POST['inworkshop'] = 0;}
                if(empty($_POST['status'])){$_POST['status'] = 0;}
                if(!empty($_POST['name'])){
                    $result = $this->Equipment->update(
                        'id', (int) $id,
                        [
                            'name' => $_POST['name'],
                            'content' => $_POST['content'],
                            'r_install_date' => $_POST['r_install_date'],
                            'r_pc_name' => $_POST['r_pc_name'],
                            'r_bios' => $_POST['r_bios'],
                            'r_manufacturer' => $_POST['r_manufacturer'],
                            'r_ram' => $_POST['r_ram'],
                            'r_cpu' => $_POST['r_cpu'],
                            'r_socket' => $_POST['r_socket'],
                            'r_mb' => $_POST['r_mb'],
                            'r_sn' => $_POST['r_sn'],
                            'r_cg' => $_POST['r_cg'],
                            'inworkshop' => $_POST['inworkshop'],
                            'serialnumber' => $_POST['serialnumber'],
                            'numberproduct' => $_POST['numberproduct'],
                            'licence_os' => $_POST['licence_os'],
                            'status' => $_POST['status'],
                            'year' => $_POST['year'],
                            'client_id' => $_POST['client_id'],
                            'brand_id' => $_POST['brand_id'],
                            'category_equipment_id' => $_POST['category_equipment_id'],
                            'operating_systems_id' => $_POST['operating_systems_id'],
                            'updated_at' => $this->newDate
                        ]);
                    if($result){
                        $this->session->setFlash('success', 'Les données ont bien été modifiées');
                        //return $this->home();
                    }
                }
            }

        $equipment = $this->Equipment->find('id', (int) $id);
        $searchClient = $this->Client->find('id', $equipment->client_id);
        $user = $this->User->find('id', $equipment->auser_id);
        // Scan le dossier BAO dans equipment
        $folder = ROOT . 'documents/bao/equipments/' . $equipment->id;
        $files = @array_diff(scandir($folder), array('..', '.'));
        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('équipements', '/admin/equipments')
            ->addCrumb($searchClient->fullname, '/admin/client/'.$searchClient->id)
            ->addCrumb($equipment->name, '');
        $this->breadcrumbs->render();

        $categories = $this->CategoryEquipment->extract('id', 'title');
        $operating = $this->OperatingSystem->extract('id', 'os_full');
        $brands = $this->Brand->extract('id', 'b_title');
        $client = $this->Client->extract('id', 'fullname');
        $client_id = $searchClient->id;
        $interventions = $this->Intervention->allById('equipment_id', (int) $id);
        $form = new SpectreForm($equipment);
        $this->render('equipments/admin/edit.twig', [
            'equipment' => $equipment,
            'categories' => $categories,
            'brands' => $brands,
            'client' => $client,
            'operating' => $operating,
            'form' => $form,
            'files' => $files,
            'interventions' => $interventions,
            'client_id' => $client_id,
            'user' => $user,
            'searchClient' => $searchClient,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);

    }

    /**
     * @param int $id
     */
    public function importRapportBao(int $id){
        $handle = new Upload($_FILES['bao_file'], 'fr_FR');
        $extension = strtolower(pathinfo($_FILES['bao_file']['name'], PATHINFO_EXTENSION));
        $authorize = array('bao', 'proaxive');
        if(!in_array($extension, $authorize)){
            $this->session->setFlash('danger', "Ce n'est pas un fichier .bao");
            return $this->edit($id);
        } else {
            $handle->no_script = false;
            if($handle->uploaded){
                $handle->file_new_name_body = str_replace('_', '-', $handle->file_src_name_body);
                $handle->process(ROOT . 'documents/bao/equipments/' . $id);
                if($handle->processed){
                    $result = $this->Equipment->update(
                        'id', (int) $id,
                        [
                            'file_bao' => $handle->file_dst_name
                        ]);
                    if($result){
                        $this->session->setFlash('success', 'Le fichier a correctement été importé.');
                        return $this->redirect('/admin/equipment/'.$id.'/bao');
                    }
                } else {
                    $this->session->setFlash('danger', 'Un problème est survenu -> ' . $handle->error);
                    $this->edit($id);
                }
            } else {
                $this->session->setFlash('danger', 'Un problème est survenu -> ' . $handle->error);
                $this->edit($id);
            }
        }
    }

    /**
     * Ajoute les détails des composants depuis le fichier .bao
     * @param int $id
     */
    public function addBao(int $id){
        $equipment = $this->Equipment->find('id', (int) $id);
        if(empty($equipment)){
            $this->session->setFlash('danger', "Cet équipement est introuvable");
            return $this->home();
        }
        if($equipment->file_bao == null){
            $this->session->setFlash('danger', "[SQL] Nom de fichier introuvable pour cet équipement");
            return $this->home();
        } else {
                // Vérifie et ouvre le fichier BAO
                $composants = new BAO();
                $check = $composants->checkPath($equipment->id, $equipment->file_bao, 'Erreur');
                if($check == false){
                    $this->session->setFlash('danger', "Le fichier .bao n'existe pas !");
                    return $this->home();
                }
                $files = $composants->parser_array($equipment->id .'/' . $equipment->file_bao);
                if(!empty($_POST['r_pc_name'])){
                    $result = $this->Equipment->update(
                        'id', (int) $id,
                        [
                            'r_install_date' => $_POST['r_install_date'],
                            'name' => $_POST['r_pc_name'],
                            'r_pc_name' => $_POST['r_pc_name'],
                            'r_bios' => $_POST['r_bios'],
                            'r_manufacturer' => $_POST['r_manufacturer'],
                            'r_ram' => $_POST['r_ram'],
                            'r_cpu' => $_POST['r_cpu'],
                            'r_socket' => $_POST['r_socket'],
                            'r_mb' => $_POST['r_mb'],
                            'r_sn' => $_POST['r_sn'],
                            'r_cg' => $_POST['r_cg'],
                            'r_hdd0' => $_POST['r_hdd0'],
                            'r_hdd1' => $_POST['r_hdd1'],
                            'r_hdd2' => $_POST['r_hdd2'],
                            'r_hdd3' => $_POST['r_hdd3'],
                            'updated_at' => $this->newDate
                        ]);
                    if($result){
                        // Supprime le fichier après sauvegarde
                        //unlink(ROOT . 'documents/bao/equipments/' . $equipment->id. '/' . $equipment->file_bao);
                        $this->session->setFlash('success', 'Les données ont bien été enregistrée');
                        return $this->edit($equipment->id);
                    }
                }
        }

        // Charge le formulaire
        $form = new SpectreForm($_POST);
        //
        $searchClient = $this->Client->find('id', $equipment->client_id);
        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('équipements', '/admin/equipments')
            ->addCrumb($searchClient->fullname, '/admin/client/'.$searchClient->id)
            ->addCrumb($equipment->name, '');
        $this->breadcrumbs->render();

        $this->render('equipments/admin/add_materials.twig', [
            'equipment' => $equipment,
            'form' => $form,
            'files' => $files,
            'searchClient' => $searchClient,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }


    /**
     * Ouvre le fichier BAO (active)
     * @param $id
     */
    public function viewCurrentBao($id){
        $equipment = $this->Equipment->find('id', (int) $id);
        $file_bao = new BAO();
        $open_bao = $file_bao->view('equipments/'. $equipment->id .'/' . $equipment->file_bao);

        $searchClient = $this->Client->find('id', $equipment->client_id);
        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('équipements', '/admin/equipments')
            ->addCrumb($searchClient->fullname, '/admin/client/'.$searchClient->id)
            ->addCrumb($equipment->name, '/admin/equipment/'. $equipment->id)
            ->addCrumb('Fichier BAO', '');
        $this->breadcrumbs->render();

        $this->render('equipments/admin/view_bao.twig', [
            'equipment' => $equipment,
            'open_bao' => $open_bao,
            'searchClient' => $searchClient,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    /**
     * Ouvre le fichier BAO (file archive)
     * @param int $id
     * @param string $filename
     */
    public function viewRapportBao(int $id, string $filename){
        $equipment = $this->Equipment->find('id', (int) $id);
        $dir = ROOT . 'documents/bao/equipments/'. $equipment->id .'/';
        $file = $dir . $filename .'.bao';
        if (file_exists($file)){
            $handle = $this->openFile($dir . $filename.'.bao', 'r');
        } else {
            $this->session->setFlash('danger', "Impossible d'ouvrir le fichier <strong>$filename.bao</strong>");
            return $this->home();
        }

        $searchClient = $this->Client->find('id', $equipment->client_id);
        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('équipements', '/admin/equipments')
            ->addCrumb($searchClient->fullname, '/admin/client/'.$searchClient->id)
            ->addCrumb($equipment->name, '/admin/equipment/'. $equipment->id)
            ->addCrumb('Fichier BAO', '');
        $this->breadcrumbs->render();

        $this->render('equipments/admin/view_bao.twig', [
            'equipment' => $equipment,
            'open_bao' => $handle,
            'searchClient' => $searchClient,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    /**
     * Permet de d'afficher les équipements d'un client
     * SELECT FORM
     * @param int $id
     */
    public function byclient(int $id)
    {
        $equipments = $this->Equipment->byclient($id);
        header('Content-Type: application/json');
        echo json_encode(array_map(function ($equipment){
            return [
                'label' => $equipment->name . ' - ' . $equipment->title,
                'value' => $equipment->eid
            ];
        }, $equipments));
    }

    /**
     * Permet d'exporter les données au format CSV
     */
    public function exportEquipment(){
        $equipments = $this->Equipment->exportWithClientCategoryBrandsOS();
        if(!empty($_POST)){
            $exportData = new CSV();
            $exportData->export_thead($equipments, 'Export-Equipments');
        }
    }

    /**
     * Permet de supprimer un équipement
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function delete(){
        App::getAuth()->isAdminOnly();
        if(!empty($_POST)){
            if (isset($_POST)) {
                $equipment = $this->Equipment->delete('id', (int) $_POST['id']);
                $this->session->setFlash('success', "L'équipement a été supprimé.");
                return $this->home();
            } else {
                $this->session->setFlash('danger', "L'équipement'n'a pas pu être supprimé.");

            }
        }
    }
}