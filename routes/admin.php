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

// 后台API接口
use Laravel\Lumen\Routing\Router;

$router->group([
    'prefix'     => 'admin',
//    'middleware' => 'cors'
], function () use ($router) {
    // 登录
    $router->get('/login', 'Admin\AdminController@login');
});
