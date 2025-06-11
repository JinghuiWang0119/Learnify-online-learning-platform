<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Login::index');
$routes->get('/hello', 'Hello::index');
$routes->get('/login', 'Login::index');
$routes->post('/login/check_login', 'Login::check_login');
$routes->get('/login/logout', 'Login::logout');
$routes->post('/upload/upload_file', 'Upload::upload_file');
$routes->get('/upload', 'Upload::index');
$routes->get('/upload/download_file/(:segment)', 'Upload::download_file/$1');
$routes->get('/movie', 'MovieController::index');
$routes->match(['get', 'post'], '/login/register', 'Login::register');
$routes->get('/profile', 'Profile::index');
$routes->get('profile/edit', 'Profile::edit');
$routes->post('profile/update', 'Profile::update');
$routes->get('/video', 'Video::index');
$routes->get('/video/play/(:num)', 'Video::play/$1');
$routes->post('video/submit_comment', 'Video::submit_comment');
$routes->get('video/get_comments', 'Video::get_comments');
$routes->post('/video/like_video', 'Video::like_video');
$routes->get('/video/get_likes', 'Video::get_likes');
$routes->post('/video/add_course', 'Video::add_course');
$routes->get('/video/search', 'Video::search');
$routes->match(['get', 'post'], '/login/forgot_password', 'Login::forgot_password');
$routes->match(['get', 'post'], 'login/change_password/(:any)', 'Login::change_password/$1');
$routes->get('login/verify_email/(:any)', 'Login::verify_email/$1');



/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}