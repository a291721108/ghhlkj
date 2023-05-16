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
    'middleware' => 'cors'
], function () use ($router) {
    // 登录
    $router->post('/login', 'Admin\AdminController@login');

    // 登录
    $router->post('/register', 'Admin\AdminController@register');

    $router->post('/recognizeBusinessLicense', 'Common\LicenseController@recognizeBusinessLicense');

    $router->post('/create', 'Common\LicenseController@create');

});


// 需要检查token
$router->group([
    'prefix'     => 'admin',
    'middleware' => [
        'auth',
        'cors',
//        'authData'
    ]
], function () use ($router) {

    // 获取用户信息
    $router->get('/getAdminInfo', 'Admin\AdminController@getAdminInfo');

    // 修改密码
    $router->post('/changePassword', 'Admin\AdminController@changePassword');

    // 获取机构房间列表
    $router->post('/getInstitutionHomeList', 'Admin\RoomController@getInstitutionHomeList');

    // 添加机构房间
    $router->post('/addInstitutionHome', 'Admin\RoomController@addInstitutionHome');

    // 预约审核
    $router->post('/noDepositAgreed', 'Admin\OrderNotificationController@noDepositAgreed');

    // 同意续费
    $router->post('/agreeRenew', 'Admin\OrderNotificationController@agreeRenew');

});
