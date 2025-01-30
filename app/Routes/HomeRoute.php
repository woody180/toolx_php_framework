<?php

use App\Engine\Libraries\Router;

$router = Router::getInstance();

$router->get('snow-fall, like-snow, snowfall, snow, fall', 'HomeController@index');
$router->get('page/gallery, page/gallery-page', 'HomeController@gallery');
$router->get('page/(:segment)/(:num)', 'HomeController@about');