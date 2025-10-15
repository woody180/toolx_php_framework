<?php

use App\Engine\Libraries\Router;

$router = Router::getInstance();

$router->get('filemanager, filemanager/(:continue)', 'FileManagerController@index', ['Middlewares/checkAjax']);
$router->post('filemanager/upload', 'FileManagerController@upload', ['Middlewares/checkAjax']);
$router->post('filemanager/create-directory', 'FileManagerController@makeDirectory', ['Middlewares/checkAjax']);
$router->post('filemanager/delete', 'FileManagerController@delete', ['Middlewares/checkAjax']);
$router->get('filemanager/get-server-info', 'FileManagerController@getServerInfo', ['Middlewares/checkAjax']);
$router->post('filemanager/compress', 'FileManagerController@compress', ['Middlewares/checkAjax']);
$router->post('filemanager/unzip', 'FileManagerController@unzip', ['Middlewares/checkAjax']);
$router->post('filemanager/rename', 'FileManagerController@rename', ['Middlewares/checkAjax']);
$router->get('filemanager/file-info', 'FileManagerController@fileInfo', ['Middlewares/checkAjax']);
$router->get('filemanager/search', 'FileManagerController@search', ['Middlewares/checkAjax']);