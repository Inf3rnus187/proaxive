<?php

use src\Router\Router;

$router = new Router($_GET['url']);
//// LOGIN DASHBAORD ////
$router->get('/'.App::getJson()->app_login_url, "UserForm#login");
$router->post('/'.App::getJson()->app_login_url, "UserForm#login");
$router->get('/login-Totp', "UserForm#loginTotp");
$router->post('/login-Totp', "UserForm#loginTotp");
$router->post('/login', "UserForm#connectWithPassword");
$router->get('/login', "UserForm#connectWithPassword");
// Logout and Locked
$router->get('/logout', 'User#logout');
$router->get('/locked', 'User#locked');
// Getting Started
$router->get('/getting-started', "GettingStarted#firstInstall");
$router->post('/getting-started', "GettingStarted#firstInstall");
// Lost Password
$router->get('/lostpassword', "UserForm#reset");
$router->post('/lostpassword', "UserForm#reset");
$router->get('/init-password/:token/:id', 'UserForm#init')->with('token', '[a-z\-0-9]+')->with('id', '[0-9]+');
$router->post('/init-password/:token/:id', 'UserForm#init')->with('token', '[a-z\-0-9]+')->with('id', '[0-9]+');

//// GET INDEX (TRACKER) ////
// Interventions
$router->get('/i/:numberlink', "Intervention#show")->with('numberlink', '[a-z\-0-9]+');
$router->get('/i/:number/pdf', 'Intervention#snappyPDF')->with('number', '[0-9]+');
$router->get('/i/:number_link/print', 'Intervention#printIntervention')->with('number_link', '[a-z\-0-9]+');
$router->get('/', "Home#home");
$router->post('/', "Home#home");
// Error
$router->get('/notfound', "Error#error404");
$router->get('/ie6-browser', "Error#browser");
//// ADMIN GET AND POST ////
$router->get('/admin', "Dashboard#AdminHome#index"); // index dashboard
// Company
$router->get('/admin/company', 'Dashboard#AdminCompany#home');
$router->get('admin/add-company', 'Dashboard#AdminCompany#add');
$router->post('admin/add-company', 'Dashboard#AdminCompany#add');
$router->get('/admin/company/:id', "Dashboard#AdminCompany#edit")->with('id', '[0-9]+');
$router->post('/admin/company/:id', "Dashboard#AdminCompany#edit")->with('id', '[0-9]+');
// Interventions
$router->get('/admin/interventions', "Dashboard#AdminIntervention#home");
$router->get('/admin/intervention/sendmail-update/:id', "Dashboard#AdminIntervention#sendMailUpdate")->with('id', '[0-9]+');
$router->get('/admin/intervention/html/:id', "Dashboard#AdminIntervention#showHtml")->with('id', '[0-9]+');
$router->get('/admin/intervention/html/print/:id', "Dashboard#AdminIntervention#showHtml")->with('id', '[0-9]+');
$router->get('/admin/intervention/pdf/:id', "Dashboard#AdminIntervention#snappyPDF")->with('id', '[0-9]+');
$router->get('/admin/intervention/:id', "Dashboard#AdminEditIntervention#edit")->with('id', '[0-9]+');
$router->post('/admin/intervention/:id', "Dashboard#AdminEditIntervention#edit")->with('id', '[0-9]+');
$router->get('/admin/intervention/:id/rapport/bao', "Dashboard#AdminEditIntervention#bao")->with('id', '[0-9]+');
$router->post('/admin/intervention/:id/rapport/bao', "Dashboard#AdminEditIntervention#bao")->with('id', '[0-9]+');
$router->get('/admin/add-intervention', "Dashboard#AdminAddIntervention#add");
$router->post('/admin/add-intervention', "Dashboard#AdminAddIntervention#add");
$router->get('/admin/equipments/byclient/:id', 'Dashboard#AdminEquipment#byclient')->with('id', '[0-9]+');
$router->get('/admin/client/:id_client/add-intervention', "Dashboard#AdminAddIntervention#addWithClient")->with('id_client', '[0-9]+');
$router->post('/admin/client/:id_client/add-intervention', "Dashboard#AdminAddIntervention#addWithClient")->with('id_client', '[0-9]+');
$router->post('/admin/client/:id_client/equipment/:id_equipment/add-intervention', "Dashboard#AdminAddIntervention#addWithClientAndEquipment")->with('id_client', '[0-9]+')->with('id_equipment', '[0-9]+');
$router->get('/admin/client/:id_client/equipment/:id_equipment/add-intervention', "Dashboard#AdminAddIntervention#addWithClientAndEquipment")->with('id_client', '[0-9]+')->with('id_equipment', '[0-9]+');
$router->post('/admin/intervention/delete', "Dashboard#AdminIntervention#delete");
$router->get('/admin/intervention/search/:key', "Dashboard#AdminIntervention#search")->with('key', '[a-z\-0-9]+');
// Outlay
$router->get('/admin/outlay', 'Dashboard#AdminOutlay#home');
$router->post('/admin/outlay/export', "Dashboard#AdminOutlay#exportOutlay");
$router->get('admin/add-outlay', 'Dashboard#AdminOutlay#add');
$router->post('admin/add-outlay', 'Dashboard#AdminOutlay#add');
$router->get('/admin/client/:id_client/add-outlay', "Dashboard#AdminOutlay#addWithClient")->with('id_client', '[0-9]+');
$router->post('/admin/client/:id_client/add-outlay', "Dashboard#AdminOutlay#addWithClient")->with('id_client', '[0-9]+');
$router->get('/admin/outlay/:id/edit', "Dashboard#AdminOutlay#edit")->with('id', '[0-9]+');
$router->post('/admin/outlay/:id/edit', "Dashboard#AdminOutlay#edit")->with('id', '[0-9]+');
$router->get('admin/outlay/html/:id', 'Dashboard#AdminOutlay#showHtml')->with('id', '[0-9]+');
// Equipments
$router->get('/admin/equipments', "Dashboard#AdminEquipment#home");
$router->post('/admin/equipments/export', "Dashboard#AdminEquipment#exportEquipment");
$router->get('admin/equipments/byclient/:id', 'Dashboard#AdminEquipment#byclient')->with('id', '[0-9]+');
$router->get('/admin/equipment/:id', "Dashboard#AdminEquipment#edit")->with('id', '[0-9]+');
$router->post('/admin/equipment/:id', "Dashboard#AdminEquipment#edit")->with('id', '[0-9]+');
$router->get('/admin/add-equipment', "Dashboard#AdminEquipment#add");
$router->post('/admin/add-equipment', "Dashboard#AdminEquipment#add");
$router->post('/admin/equipment/delete', "Dashboard#AdminEquipment#delete");
$router->get('/admin/client/:id_client/add-equipment', "Dashboard#AdminEquipment#addWithClient")->with('id_client', '[0-9]+');
$router->post('/admin/client/:id_client/add-equipment', "Dashboard#AdminEquipment#addWithClient")->with('id_client', '[0-9]+');
$router->post('/admin/equipment/:id/import', "Dashboard#AdminEquipment#importRapportBao")->with('id', '[0-9]+');
$router->post('/admin/equipment/:id/bao', "Dashboard#AdminEquipment#addBao")->with('id', '[0-9]+');
$router->get('/admin/equipment/:id/bao', "Dashboard#AdminEquipment#addBao")->with('id', '[0-9]+');
$router->get('/admin/equipment/:id/bao/view', "Dashboard#AdminEquipment#viewCurrentBao")->with('id', '[0-9]+');
$router->get('/admin/equipment/:id/bao/:filename', 'Dashboard#AdminEquipment#viewRapportBao')->with('id', '[0-9]+')->with('filename', '[a-z\-0-9]+');
$router->get('/admin/equipment/search/:key', "Dashboard#AdminEquipment#search")->with('key', '[a-z\-0-9]+');
// Equipments categories
$router->get('/admin/equipments/categories', "Dashboard#AdminCategoriesEquipment#home");
$router->post('/admin/equipments/categories', "Dashboard#AdminCategoriesEquipment#home");
//$router->get('/admin/equipments/category/:id', "Dashboard#AdminCategoriesEquipment#edit")->with('id', '[0-9]+');
$router->post('/admin/equipments/category/:id', "Dashboard#AdminCategoriesEquipment#edit")->with('id', '[0-9]+');
$router->post('/admin/equipments/category/delete', "Dashboard#AdminCategoriesEquipment#delete");
// Equipments brands
$router->get('/admin/equipments/brands', "Dashboard#AdminBrand#home");
$router->post('/admin/equipments/brands', "Dashboard#AdminBrand#home");
$router->get('/admin/equipments/edit-brand/:id', "Dashboard#AdminBrand#edit")->with('id', '[0-9]+');
$router->post('/admin/equipments/edit-brand/:id', "Dashboard#AdminBrand#edit")->with('id', '[0-9]+');
$router->post('/admin/equipments/brand/delete', "Dashboard#AdminBrand#delete");
// Equipments Operating System
$router->get('/admin/operating-systems', "Dashboard#AdminOperatingSystem#home");
$router->post('/admin/operating-systems', "Dashboard#AdminOperatingSystem#home");
$router->get('/admin/operating-system/:id', "Dashboard#AdminOperatingSystem#edit")->with('id', '[0-9]+');
$router->post('/admin/operating-system/:id', "Dashboard#AdminOperatingSystem#edit")->with('id', '[0-9]+');
$router->post('/admin/operating-system/delete', "Dashboard#AdminOperatingSystem#delete")->with('id', '[0-9]+');
// Company
$router->get('/admin/add-company', 'Dashboard#AdminCompany#add');
$router->post('/admin/add-company', 'Dashboard#AdminCompany#add');
$router->get('/admin/mycompany', "Dashboard#AdminCompany#edit");
$router->post('/admin/mycompany', "Dashboard#AdminCompany#edit");
// Status
$router->get('/admin/status', "Dashboard#AdminStatus#home");
$router->post('/admin/status', "Dashboard#AdminStatus#home");
$router->get('/admin/edit-status/:id', "Dashboard#AdminStatus#edit")->with('id', '[0-9]+');
$router->post('/admin/edit-status/:id', "Dashboard#AdminStatus#edit")->with('id', '[0-9]+');
$router->post('/admin/status/deleted', "Dashboard#AdminStatus#delete");
// Departments
$router->get('/admin/departments', "Dashboard#AdminDepartment#home");
$router->post('/admin/departments', "Dashboard#AdminDepartment#home");
$router->get('/admin/edit-department/:id', "Dashboard#AdminDepartment#edit")->with('id', '[0-9]+');
$router->post('/admin/edit-department/:id', "Dashboard#AdminDepartment#edit")->with('id', '[0-9]+');
// Client
$router->get('/admin/clients', "Dashboard#AdminClient#home");
$router->post('/admin/clients/export', "Dashboard#AdminClient#exportClient");
$router->get('/admin/add-client', "Dashboard#AdminClient#add");
$router->post('/admin/add-client', "Dashboard#AdminClient#add");
$router->get('/admin/client/:id/edit', "Dashboard#AdminClient#edit")->with('id', '[0-9]+');
$router->post('/admin/client/:id/edit', "Dashboard#AdminClient#edit")->with('id', '[0-9]+');
$router->get('/admin/client/:id', "Dashboard#AdminClient#view")->with('id', '[0-9]+');
$router->get('/admin/cities/search/:key', "Dashboard#AdminClient#searchCities")->with('key', '[a-z\-0-9]+');
$router->post('/admin/client/delete', "Dashboard#AdminClient#delete");
$router->get('/admin/client/search/:key', "Dashboard#AdminClient#search")->with('id', '[a-z\-0-9]+');
// Society
$router->get('/admin/society', "Dashboard#AdminSociety#home");
$router->post('/admin/society/export', "Dashboard#AdminSociety#exportSociety");
$router->get('/admin/add-society', "Dashboard#AdminSociety#add");
$router->post('/admin/add-society', "Dashboard#AdminSociety#add");
$router->get('/admin/society/:id', "Dashboard#AdminSociety#edit")->with('id', '[0-9]+');
$router->post('/admin/society/:id', "Dashboard#AdminSociety#edit")->with('id', '[0-9]+');
$router->post('/admin/society/delete', "Dashboard#AdminSociety#delete");
/* God Users and tech */
$router->get('/admin/ausers', "Dashboard#AdminAuser#home");
$router->get('/admin/ausers/totp', "Dashboard#AdminAuser#totp");
$router->post('/admin/ausers/totp', "Dashboard#AdminAuser#totp");
$router->post('/admin/auser/totp/desactivate', "Dashboard#AdminAuser#totpDesactivate");
$router->get('/admin/add-auser', "Dashboard#AdminAuser#add");
$router->post('/admin/add-auser', "Dashboard#AdminAuser#add");
$router->get('/admin/auser/:id/edit', "Dashboard#AdminAuser#edit")->with('id', '[0-9]+');
$router->post('/admin/auser/:id/edit', "Dashboard#AdminAuser#edit")->with('id', '[0-9]+');
$router->post('/admin/auser/delete', "Dashboard#AdminAuser#delete");
// MyAccount
$router->post('/admin/myaccount', "Dashboard#AdminMyAccount#home");
$router->get('/admin/myaccount', "Dashboard#AdminMyAccount#home");
$router->post('/admin/myaccount/edit', "Dashboard#AdminMyAccount#edit");
$router->get('/admin/myaccount/edit', "Dashboard#AdminMyAccount#edit");
$router->post('/admin/myaccount/password', "Dashboard#AdminMyAccount#password");
$router->get('/admin/myaccount/password', "Dashboard#AdminMyAccount#password");
$router->get('/admin/myaccount/totp', "Dashboard#AdminMyAccount#totp");
$router->post('/admin/myaccount/totp', "Dashboard#AdminMyAccount#totp");
// Setting
$router->get('/admin/setting', 'Dashboard#AdminSetting#home');
$router->post('/admin/setting', 'Dashboard#AdminSetting#home');
$router->get('/admin/setting/courriel', 'Dashboard#AdminSetting#courriel');
$router->post('/admin/setting/courriel', 'Dashboard#AdminSetting#courriel');
$router->get('/admin/setting/database', 'Dashboard#AdminSetting#database');
$router->post('/admin/setting/database', 'Dashboard#AdminSetting#database');
$router->get('/admin/setting/css', 'Dashboard#AdminSetting#homeTheme');
$router->post('/admin/setting/css', 'Dashboard#AdminSetting#homeTheme');
$router->get('/admin/setting/css/:theme', 'Dashboard#AdminSetting#viewCSS')->with('theme', '[a-z\-0-9]+');
$router->post('/admin/setting/css/:theme', 'Dashboard#AdminSetting#viewCSS')->with('theme', '[a-z\-0-9]+');
$router->get('/admin/setting/css/:theme/:css', 'Dashboard#AdminSetting#editCSS')->with('theme', '[a-z\-0-9]+')->with('css', '[a-z\-0-9]+');
$router->post('/admin/setting/css/:theme/:css', 'Dashboard#AdminSetting#editCSS')->with('theme', '[a-z\-0-9]+')->with('css', '[a-z\-0-9]+');
// Error and perms
$router->get('/admin/needperms', 'Dashboard#AdminRedirect#needperms');
$router->get('/admin/notfound', 'Dashboard#AdminRedirect#notfound');
// Stickies
$router->get('/admin/sticky', "Dashboard#AdminSticky#index");
$router->get('/admin/add-sticky', "Dashboard#AdminSticky#add");
$router->post('/admin/add-sticky', "Dashboard#AdminSticky#add");
$router->get('/admin/sticky/:id', "Dashboard#AdminSticky#edit")->with('id', '[0-9]+');
$router->post('/admin/sticky/:id', "Dashboard#AdminSticky#edit")->with('id', '[0-9]+');
$router->post('/admin/sticky/delete', "Dashboard#AdminSticky#delete");
$router->post('/admin/sticky/all-delete', "Dashboard#AdminSticky#deleteAll");
// Update App
$router->get('/database-update', 'ProaxiveUpdate#updateDatabase');
$router->post('/database-update', 'ProaxiveUpdate#updateDatabase');
$router->post('/database-save', 'ProaxiveUpdate#mysqldumpData');
$router->run();