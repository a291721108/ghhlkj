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
    'middleware' => 'cors'
], function () use ($router) {
    // 登录
    $router->post('/login', 'Front\AuthController@login');

    // 发送短信验证吗
    $router->post('/sendSms', 'Common\SendMsgController@sendSms');

    // 忘记密码
    $router->post('/forgotPassword', 'Front\AuthController@forgotPassword');

    //验证码登录
    $router->post('/sendSmsLogin', 'Front\AuthController@sendSmsLogin');

    //用户消息修改
    $router->post('/register', 'Front\AuthController@register');

    // 图片上传
    $router->post('/saveFile', 'Common\UploadController@saveFile');

    // ceshi
    $router->get('/test', 'Front\OrganizationController@test');
});

// 需要检查token
$router->group([
    'prefix'     => 'api',
    'middleware' => [
        'auth',
//        'log',
        'cors'
    ]
], function () use ($router) {
    // 账号注销
    $router->post('/closeAnAccount', 'Front\AuthController@closeAnAccount');

    // 身份证正面
    $router->post('/fontPhotoCard', 'Front\AuthController@fontPhotoCard');

    // 身份证反面
    $router->post('/backPhotoCard', 'Front\AuthController@backPhotoCard');

    // 安全退出
    $router->post('/safeWithdrawing', 'Front\AuthController@safeWithdrawing');

    // 机构列表展示
    $router->get('/organizationList', 'Front\OrganizationController@organizationList');

    // 机构列表展示浏览量最高的5个
    $router->get('/tissueCount', 'Front\OrganizationController@tissueCount');

    // 机构详情
    $router->get('/tissueDetailPage', 'Front\HomeTypeController@tissueDetailPage');

    // 获取房间类型
    $router->get('/homeTypeList', 'Front\HomeTypeController@homeTypeList');

    // 首页轮播图
    $router->get('/slideshow', 'Front\HomeImgController@slideshow');
});
