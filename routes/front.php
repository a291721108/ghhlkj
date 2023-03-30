<?php
/** @var Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Laravel\Lumen\Routing\Router;

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group([
    'prefix'     => 'api',
//    'middleware' => 'cors'
], function () use ($router) {
    // 登录
    $router->post('/login', 'Front\AuthController@login');
});

// 需要检查token
$router->group([
    'prefix'     => 'api',
    'middleware' => [
        'auth',
        'log',
        'cors'
    ]
], function () use ($router) {

    // 获取用户爱好列表
    $router->get('/list', 'Front\AuthController@list');
});
