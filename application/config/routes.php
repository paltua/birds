<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'account/auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route[ADMIN_NAME] = 'admin/auth';
$route[ADMIN_NAME] = 'admin/auth';
$route[ADMIN_NAME.'/dashboard'] = 'admin/dashboard';
$route[ADMIN_NAME.'/auth/logout'] = 'admin/auth/logout';
$route[ADMIN_NAME.'/user'] = 'admin/user/index';
$route[ADMIN_NAME.'/user/adduser'] = 'admin/user/adduser';
$route[ADMIN_NAME.'/user/save'] = 'admin/user/save';
$route[ADMIN_NAME.'/user/delete/(:num)'] = 'admin/user/delete/$1';
$route[ADMIN_NAME.'/user/edit/(:num)'] = 'admin/user/edit/$1';
$route[ADMIN_NAME.'/user/edituser'] = 'admin/user/edituser';
$route[ADMIN_NAME.'/user/Ajax_CheckEmail'] = 'admin/user/Ajax_CheckEmail';

$route[ADMIN_NAME.'/query'] = 'admin/query/index';
$route[ADMIN_NAME.'/query/ajax_Modallist'] = 'admin/query/ajax_Modallist';

$route[ADMIN_NAME.'/auth/forgotPassword'] = 'admin/auth/forgotPassword';
$route[ADMIN_NAME.'/auth/forgotPasswordEmail'] = 'admin/auth/forgotPasswordEmail';
$route[ADMIN_NAME.'/auth/resetPassword/(:any)/(:any)'] = 'admin/auth/resetPassword/$1/$2';
