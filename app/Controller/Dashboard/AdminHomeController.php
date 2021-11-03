<?php
/*
 * AdminHomeController Class
 *
 * @author SelMaK - synexolabs@gmail.com - https://www.synexolabs.com
 * @version 1.0
 * @date May 21, 2021
 */
namespace App\Controller\Dashboard;

use App;
use Creitive\Breadcrumbs\Breadcrumbs;
use src\Auth\Auth;
use src\MyClass\Session;

class AdminHomeController extends AppController
{

    /**
     * @var string
     */
    private $current_menu;

    /**
     * @var Breadcrumbs
     */
    private Breadcrumbs $breadcrumbs;

    private Auth $auth;

    /**
     * @var Session
     */
    private Session $session;

    public function __construct()
    {
        parent::__construct();
        $this->current_menu = 'home';
        $this->auth = new Auth(app::getInstance()->getDB());
        $this->session = Session::getSessionInstance();
        $this->breadcrumbs = new Breadcrumbs();
        // Load Model
        $this->loadModel('Client');
        $this->loadModel('Equipment');
        $this->loadModel('Intervention');
        $this->loadModel('Brand');
        $this->loadModel('Company');
        $this->loadModel('Status');
        $this->loadModel('User');
        $this->loadModel('Outlay');
        $this->loadModel('Sticky');
    }

    /**
     * Chargement de l'index
     */

    public function index(){
        $countEquipment = $this->Equipment->countRow();
        $interInProgress = $this->Intervention->selectWithClientInProgressByAuser(4, (int) $this->auth->getUserId());
        $interInClose = $this->Intervention->selectWithClientInCloseByAuser(4, (int) $this->auth->getUserId());
        $countIntervention = $this->Intervention->countRow();
        $myCountInter = $this->Intervention->countRowWhere($this->auth->getUserId(), 'auser_id');
        $myCountOutlay = $this->Outlay->countRowWhere($this->auth->getUserId(), 'auser_id');
        $countClient = $this->Client->countRow();
        $clients = $this->Client->findAllWithLimit(4);
        $countBrand = $this->Brand->countRow();
        $countCompany = $this->Company->CountRow();
        $countStatus = $this->Status->CountRow();
        $countOutlay = $this->Outlay->CountRow();
        $countSticky = $this->Sticky->CountRow();
        $equipmentInWork = $this->Equipment->selectWithCategoryInWork(4);
        $stickyStick = $this->Sticky->selectByStick(8);
        $auser = $this->User->find('id', 1);

        $auth = $this->auth;

        $this->breadcrumbs->addCrumb('Panel Proaxive', '/admin')
            ->addCrumb('Accueil', '');
        $this->breadcrumbs->render();

        $folderInstall = ROOT . '/install';
        $checkFolderInstall = file_exists($folderInstall);

        $this->render('dashboard/home/home.twig', [
            'countEquipment' => $countEquipment,
            'countIntervention' => $countIntervention,
            'countClient' => $countClient,
            'countOutlay' => $countOutlay,
            'countSticky' => $countSticky,
            'myCountInter' => $myCountInter,
            'myCountOutlay' => $myCountOutlay,
            'clients' => $clients,
            'interInProgress' => $interInProgress,
            'interInClose' => $interInClose,
            'equipmentInWork' => $equipmentInWork,
            'countBrand' => $countBrand,
            'countCompany' => $countCompany,
            'countStatus' => $countStatus,
            'stickyStick' => $stickyStick,
            'checkFolderInstall' => $checkFolderInstall,
            'auser' => $auser,
            'auth' => $auth,
            'current_menu' => $this->current_menu,
            'breadcrumbs' => $this->breadcrumbs
        ]);

    }

    /**
     * API Last version
     */
    private function app_lastversion(){
        $viewJsonFile = file_get_contents('https://proaxive.fr/version.json');
        return $app_last = json_decode($viewJsonFile, false);
    }

}