<?php

use App\Engine\Libraries\Router;

$router = Router::getInstance();

$router->get('/', 'HomeController@index');
$router->get('snow-fall, like-snow, snowfall, snow, fall', function($req, $res) {
    echo "Snow is falling";
});
$router->get('page/gallery, page/gallery-page', 'HomeController@gallery');
$router->get('page/(:segment)/(:num)', 'HomeController@about');