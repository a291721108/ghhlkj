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

    // 验证码校验
    $router->post('/codeLogin', 'Admin\AdminController@codeLogin');

    // 营业执照识别
    $router->post('/addLicense', 'Admin\AdminController@addLicense');

    // 修改密码
    $router->post('/changePassword', 'Admin\AdminController@changePassword');

    // 营业执照识别
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


    // 机构添加
    $router->post('/addInstitution', 'Admin\InstitutionController@addInstitution');

    // 机构编辑
    $router->post('/upInstitution', 'Admin\InstitutionController@upInstitution');

    // 机构查看
    $router->post('/getInstitution', 'Admin\InstitutionController@getInstitution');

    // 添加房间类型
    $router->post('/addHomeType', 'Admin\RoomTypeController@addHomeType');

    // 根据id获取房间类型
    $router->post('/homeTypeInfo', 'Admin\RoomTypeController@homeTypeInfo');

    // 修改房间类型
    $router->post('/upHomeType', 'Admin\RoomTypeController@upHomeType');

    // 获取房间类型列表
    $router->post('/getHomeType', 'Admin\RoomTypeController@getHomeType');

    // 获取机构房间列表
    $router->post('/getInstitutionHomeList', 'Admin\RoomController@getInstitutionHomeList');

    // 添加机构房间
    $router->post('/addInstitutionHome', 'Admin\RoomController@addInstitutionHome');

    // 禁用房间号
    $router->post('/delInstitutionHome', 'Admin\RoomController@delInstitutionHome');

    // 订单列表
    $router->post('/getOrderList', 'Admin\OrderNotificationController@getOrderList');

    // 预约审核
    $router->post('/noDepositAgreed', 'Admin\OrderNotificationController@noDepositAgreed');

    // 同意续费
    $router->post('/agreeRenew', 'Admin\OrderNotificationController@agreeRenew');

    // 同意退款
    $router->post('/agreeRefund', 'Admin\OrderNotificationController@agreeRefund');

    // 拒绝退款
    $router->post('/refusalRefund', 'Admin\OrderNotificationController@refusalRefund');

    // 总览
    $router->post('/overview', 'Admin\AggregateController@overview');

});
