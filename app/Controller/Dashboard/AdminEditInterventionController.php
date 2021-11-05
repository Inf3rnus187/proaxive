<?php
/*
 * AdminEditInterventionController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date October 20, 2021
 */
namespace App\Controller\Dashboard;

use \App;
use Creitive\Breadcrumbs\Breadcrumbs;
use src\Entity\Entity;
use src\MyClass\SendMail;
use src\MyClass\Str;
use src\Auth\Auth;
use src\Form\SpectreForm;
use src\MyClass\Session;
use Verot\Upload\Upload;

class AdminEditInterventionController extends AppController
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
        $this->loadModel('Society');

    }

    /**
     * Permet d'éditer et de fermer une intervention
     * @param int $id
     * @return type
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    public function edit(int $id){
        $intervention = $this->Intervention->find('id', (int) $id);

        // Permet de vérifier l'auteur de l'intervention
        // Accorde l'accès aux administrateurs
        if($intervention->auser_id != $this->auth->getUserId() AND $this->session->read('auth')->roles_id != 1){
            $this->session->setFlash('danger', "Vous n'avez pas l'autorisation de modifier cette intervention");
            $this->redirect('/admin/intervention/html/'.$intervention->id);
        }

        $client = $this->Client->find('id', $intervention->client_id);
        $equipment = $this->Equipment->find('id', $intervention->equipment_id);
        $brand = $this->Brand->find('id', $equipment->brand_id);
        $operatingSystem = $this->OperatingSystem->find('id', $equipment->operating_systems_id);
        $categoryEquipment = $this->CategoryEquipment->find('id', $equipment->category_equipment_id);
        $statusFind = $this->Status->find('id', $intervention->status_id);
        $clientFullName = Entity::cleanText($client->fullname);
        $technician = $this->User->find('id', $intervention->auser_id);
        $society = $this->Society->find('id', $client->society_id);

        // Si ok, le script continu
        if (!empty($_POST)) {
            // Met à jours les informations principales
            if(isset($_POST['update'])){
                if(empty($_POST['pmad'])){$_POST['pmad'] = 0;}else{$_POST['steps'] = null;}
                $result = $this->Intervention->update(
                    'id', (int) $id,
                    [
                        'title' => $_POST['title'],
                        'status_id' => $_POST['status_id'],
                        'details' => $_POST['details'],
                        'steps' => $_POST['steps'],
                        'inter_type' => $_POST['inter_type'],
                        'description' => $_POST['description'],
                        'note_tech' => $_POST['note_tech'],
                        'auser_id' => $_POST['auser_id'],
                        'observation' => $_POST['observation']
                    ]);
                if($_POST['steps'] == 2){
                    $this->Equipment->update(
                        'id', (int) $intervention->equipment_id,
                        [
                            'inworkshop' => 1
                        ]);
                }
                if($result){
                    $this->session->setFlash('success', "Les données ont bien été modifiées !");
                    return $this->redirect('/admin/intervention/'.$id);
                }
            }

            // Ferme l'intervention + ajoute la date de cloture
            if (isset($_POST['cloture'])) {
                if($intervention->start == null ){
                    $this->session->setFlash('danger', "L'intervention n'a pas démarrée !");
                } else {
                    $result = $this->Intervention->update(
                        'id', (int) $id,
                        [
                            'report' => $_POST['report'],
                            'closed' => 1,
                            'status_id' => 8,
                            'steps' => 4,
                            'close_date' => $this->newDate
                        ]);
                    if($result){
                        $this->session->setFlash('success', "L'intervention a bien été cloturée !");
                        return $this->redirect('/admin/intervention/'.$id);
                    }
                }
            }

            if (isset($_POST['send_file'])) {
                $baoFiles = str_replace(' ', '-', $_FILES['baofiles']);
                $handle = new Upload($baoFiles, 'fr_FR');

                if($handle->uploaded){
                    $handle->mime_check = true;
                    $handle->allowed = array('text/*');
                    $handle->file_max_size = '60024';
                    $handle->process(ROOT . 'documents/clients/'.$clientFullName.'/bao');
                    if($handle->processed){
                        $updateRapport = $this->Intervention->update(
                            'id', (int) $id,
                            [
                                'rapport_bao' => $handle->file_dst_name
                            ]
                        );
                        if($updateRapport){
                            $this->session->setFlash('success', 'Le rapport BAO a bien été chargé');
                            return $this->redirect('/admin/intervention/'.$id);
                        }
                    } else {
                        $this->session->setFlash('danger', 'Un problème est survenu -> ' . $handle->error);
                    }
                } else {
                    $this->session->setFlash('danger', 'Un problème est survenu -> ' . $handle->error);
                }
            }

            // permet d'envoyer un mail au destinataire
            // Ne pas utiliser pour le moment
            if (isset($_POST['sendmail'])) {
                $this->sendMailUpdate($intervention->id);
                $this->session->setFlash('success', "Le courriel a été envoyé à " . $client->mail);
                return $this->redirect('/admin/intervention/'.$id);
            }

            // Permet d'ajouter la date de dépôt (remise du matériel au client)
            if (isset($_POST['backhome'])) {
                $result = $this->Intervention->update(
                    'id', (int) $id,
                    [
                        'back_home' => $_POST['back_home']
                    ]);
                // Met à jour le champs "inworkshop" de l'équipement
                $updateEquipment = $this->Equipment->update(
                    'id', (int) $intervention->equipment_id,
                    [
                        'inworkshop' => 0
                    ]);
                if($result AND $updateEquipment){
                    $this->session->setFlash('success', "Confirmation que l'équipement a été remis au client !");
                    return $this->redirect('/admin/intervention/'.$id);
                }
            }

            // Inscrit la date de début d'intervention
            if (isset($_POST['start_inter'])) {
                $result = $this->Intervention->update(
                    'id', (int) $id,
                    [
                        'start' => $this->newDate
                    ]);
                if($result){
                    $this->session->setFlash('success', "Confirmation que l'intervention vient de débuter !");
                    return $this->redirect('/admin/intervention/'.$id);
                }
            }

            // Met à jour la date de l'intervention
            if (isset($_POST['update_inter'])) {
                $result = $this->Intervention->update(
                    'id', (int) $id,
                    [
                        'updated_at' => $this->newDate
                    ]);
                if($result){
                    $this->session->setFlash('success', "La date a correctement été mise à jour !");
                    //return $this->home();
                }
            }
            // Charge la photo principale de l'intervention
            if(isset($_POST['i_photo'])){
                $this->uploadPhotoMaster($intervention->id);
            }

            // Charge des photos dans la galerie de l'intervention
            if(isset($_POST['i_gallery'])){
                $this->uploadPhotosGallery($intervention->id);
            }
        }
        $file = ROOT . "documents/clients/$clientFullName/bao/" .$intervention->rapport_bao;
        // Vérifie sur le fichier existe bien
        if(is_file($file)){
            $rapport = $this->openFile($file, 'r');
        } else {
            $rapport = false;
        }

        // Récupère les photos de la galerie
        $folder = ROOT . 'public/uploads/intervention/'.$intervention->id.'/gallery';
        $isfile_exists = file_exists($folder);
        if($isfile_exists){
            $photos = array_diff(scandir($folder), array('..', '.'));
        } else {
            $photos = false;
        }

        $status = $this->Status->extract('id', 'title');
        $ausers = $this->User->extract('id', 'fullname');

        //Breadcrumb
        $this->breadcrumbs->addCrumb('Accueil', '/admin')
            ->addCrumb('Interventions', '/admin/interventions')
            ->addCrumb($client->fullname, '/admin/client/'.$client->id)
            ->addCrumb($equipment->name, $equipment->urlAdmin)
            ->addCrumb($intervention->title, '/admin/intervention/'.$intervention->urlAdmin);
        $this->breadcrumbs->render();

        $form = new SpectreForm($intervention);

        $this->render('intervention/admin/edit.twig', [
            'current_menu' => $this->current_menu,
            'intervention' => $intervention,
            'client' => $client,
            'equipment' => $equipment,
            'brand' => $brand,
            'operatingSystem' => $operatingSystem,
            'categoryEquipment' => $categoryEquipment,
            'status' => $status,
            'statusFind' => $statusFind,
            'form' => $form,
            'rapport' => $rapport,
            'photos' => $photos,
            'ausers' => $ausers,
            'technician' => $technician,
            'society' => $society,
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
                return $this->redirect('/admin/intervention/'.$id);
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
     * Permet de charger une photo principale
     */
    private function uploadPhotoMaster(int $id)
    {
        $handle = new Upload($_FILES['i_photo'], 'fr_FR');
        if($handle->uploaded){
            $handle->allowed = array('image/*');
            $handle->process(ROOT . 'public/uploads/intervention/'. $id);
            if($handle->processed){
                $uploadPhoto = $this->Intervention->update(
                    'id', (int) $id,
                    [
                        'i_photo' => $handle->file_src_name
                    ]
                );
                if($uploadPhoto){
                    $this->session->setFlash('success', 'La photo a bien été chargée');
                }
            } else {
                $this->session->setFlash('danger', 'Un problème est survenu -> ' . $handle->error);
            }
        } else {
            $this->session->setFlash('danger', 'Un problème est survenu -> ' . $handle->error);
        }
    }

    /**
     * Permet d'ajouter plusieurs photos à une intervention
     */
    private function uploadPhotosGallery(int $id)
    {
        if(!empty($_FILES)){
            $files = array();
            foreach ($_FILES['i_gallery'] as $k => $l) {
                foreach ($l as $i => $v) {
                    if (!array_key_exists($i, $files))
                        $files[$i] = array();
                    $files[$i][$k] = $v;
                }
            }
            foreach ($files as $file) {
                $handle = new Upload($file);
                if ($handle->uploaded) {
                    $handle->dir_auto_create = true;
                    $handle->Process(ROOT . 'public/uploads/intervention/'.$id.'/gallery');
                    if ($handle->processed) {
                        $this->session->setFlash('success', 'Les photos ont bien été envoyées');
                    } else {
                        $this->session->setFlash('danger', $handle->error);
                    }
                } else {
                    $this->session->setFlash('danger', $handle->error);
                }
                unset($handle);
            }
        }
    }

    /**
     * Permet d'alerter le client par mail, d'une nouvelle mise à jour de l'intervention
     * Utilise l'API de Mailjet (compte Mailjet obligatoire)
     * Ou utilise SwiftMailer (configuration manuelle)
     * A configurer via le fichier de configuration .env (Proaxive Net Install)
     * @param int $id
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function sendMailUpdate(int $id)
    {
        // Récupère les données de l'intervention
        $intervention = $this->Intervention->findByKeyAndValue('id', (int) $id);
        $client = $this->Client->find('id', $intervention->client_id);
        $user = $this->User->find('id', $intervention->auser_id);
        $equipment = $this->Equipment->find('id', $intervention->equipment_id);
        $brand = $this->Brand->find('id', $equipment->brand_id);
        $operatingSystem = $this->OperatingSystem->find('id', $equipment->operating_systems_id);
        $categoryEquipment = $this->CategoryEquipment->find('id', $equipment->category_equipment_id);
        $status = $this->Status->find('id', $intervention->status_id);
        $company = $this->Company->isDefault();
        // Lecture du fichier de configuration json (Mail)
        $viewJsonFile = file_get_contents(ROOT. '/config/mail.json');
        $viewJson = json_decode($viewJsonFile, false);
        // Lecture du fichier de configuration json (Setting)
        $viewJsonFile = file_get_contents(ROOT. '/config/setting.json');
        $viewJsonSetting = json_decode($viewJsonFile, false);
        // Charge la class SendMail
        $sendMail = new SendMail();
        // Vérifie le choix d'envoi de mail (fichier .env)
        if ($viewJson->mail_service == 'mailjet'){
            // Envoi le mail avec le service externe Mailjet
            $sendMail->sendDataMailJet('Votre suivi intervention', $client->mail, $client->fullname, [
                'intervention' => $intervention,
                'client' => $client,
                'user' => $user,
                'company' => $company,
                'equipment' => $equipment,
                'brand' => $brand,
                'status' => $status,
                'operatingSystem' => $operatingSystem,
                'categoryEquipment' => $categoryEquipment,
                'viewJsonSetting' => $viewJsonSetting
            ], 'templates/mail_updateintervention.twig');
        } elseif ($viewJson->mail_service == 'swiftmailer'){
            // Envoi le mail avec Swiftmailer
            $sendMail->sendDataSwiftMailer('Votre suivi intervention', $client->mail, $company->mail, $company->cname, [
                'intervention' => $intervention,
                'client' => $client,
                'user' => $user,
                'company' => $company,
                'equipment' => $equipment,
                'brand' => $brand,
                'status' => $status,
                'operatingSystem' => $operatingSystem,
                'categoryEquipment' => $categoryEquipment,
                'viewJsonSetting' => $viewJsonSetting
            ], 'templates/mail_updateintervention.twig');
        }
    }
}