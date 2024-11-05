<?php

use App\Engine\Libraries\Router;

$router = Router::getInstance();

$router->get('/', 'HomeController@index');
$router->get('page/gallery', 'HomeController@gallery');
$router->get('page/(:segment)/(:num)', 'HomeController@about');