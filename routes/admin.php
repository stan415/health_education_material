<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**@var Router $router*/

$router->post('/login/login', 'Admin\LoginController@login');
$router->get('/download/test', 'Admin\DownloadController@test');
$router->get('/download/tt', function () {
    return response()->streamDownload(function () {
        echo file_get_contents("https://xbjk.oss-cn-shenzhen.aliyuncs.com/material/628dee9d5ef88.png");
    }, 'test.png');
});


$router->group(['middleware' => ['verify.permission']], function () use ($router) {

    $router->get('/login/logout', 'Admin\LoginController@logout');
    $router->post('/login/change', 'Admin\LoginController@change');

    $router->get('/user/index', 'Admin\UserController@index');
    $router->post('/user/create', 'Admin\UserController@create');
    $router->post('/user/edit', 'Admin\UserController@edit');
    $router->get('/user/detail', 'Admin\UserController@detail');
    $router->get('/user/del', 'Admin\UserController@del');

    $router->post('/user/like', 'Admin\UserController@like');

    $router->get('/files/index', 'Admin\FilesController@index');
    $router->post('/files/create', 'Admin\FilesController@create');
    $router->post('/files/edit', 'Admin\FilesController@edit');
    $router->get('/files/detail', 'Admin\FilesController@detail');
    $router->get('/files/del', 'Admin\FilesController@del');

    $router->get('/videos/index', 'Admin\VideosController@index');
    $router->post('/videos/create', 'Admin\VideosController@create');
    $router->post('/videos/edit', 'Admin\VideosController@edit');
    $router->get('/videos/detail', 'Admin\VideosController@detail');
    $router->get('/videos/del', 'Admin\VideosController@del');

    $router->get('/upload/list', 'Admin\UploadController@list');
    $router->get('/upload/cover', 'Admin\UploadController@cover');

    $router->get('/download/list', 'Admin\DownloadController@list');
});
