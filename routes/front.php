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

    // 首页轮播图
    $router->get('/slideshow', 'Front\HomeImgController@slideshow');

    // 机构列表展示
    $router->get('/organizationList', 'Front\OrganizationController@organizationList');

    // 通过id获取机构详情列表
    $router->post('/organizationDetails', 'Front\OrganizationController@organizationDetails');

    // 通过机构类型id获取机构类型详情
    $router->post('/organizationTypeDetails', 'Front\HomeTypeController@organizationTypeDetails');

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

    // 身份证正面识别
    $router->post('/positiveRecognition', 'Front\AuthController@positiveRecognition');

    // 身份证反面识别
    $router->post('/negativeRecognition', 'Front\AuthController@negativeRecognition');

    // 认证录入
    $router->post('/authenticationEntry', 'Front\AuthController@authenticationEntry');

    // 安全退出
    $router->post('/safeWithdrawing', 'Front\AuthController@safeWithdrawing');

    // 个人二维码
    $router->post('/qrCode', 'Common\PersonalCodeController@qrCode');


    // 机构列表展示浏览量最高的5个
    $router->get('/tissueCount', 'Front\OrganizationController@tissueCount');





    // 获取房间类型
    $router->post('/homeTypeList', 'Front\HomeTypeController@homeTypeList');

    // 机构预约
//    $router->post('/agencyAppointment', 'Front\BookingController@agencyAppointment');

    // 预约列表
    $router->get('/reservationList', 'Front\BookingController@reservationList');

    // 获取订单详情
    $router->post('/userReservationRecord', 'Front\OrderController@userReservationRecord');

    // 下单
    $router->post('/placeAnOrder', 'Front\OrderController@placeAnOrder');

    // 订单列表
    $router->post('/orderList', 'Front\OrderController@orderList');

    // 亲友状态获取
    $router->get('/getRelativeStatus', 'Front\FriendController@getRelativeStatus');

    // 添加亲友
    $router->post('/relativeStatusAdd', 'Front\FriendController@relativeStatusAdd');

    // 亲友列表
    $router->get('/relativeStatusList', 'Front\FriendController@relativeStatusList');

    // 删除亲友
    $router->post('/relativeStatusDel', 'Front\FriendController@relativeStatusDel');

    // 编辑亲友
    $router->post('/relativeStatusUp', 'Front\FriendController@relativeStatusUp');

    // 通过id获取亲友详情
    $router->post('/getRelative', 'Front\FriendController@getRelative');

    // 添加意见反馈
    $router->post('/feedbackAdd', 'Front\FeedbackController@feedbackAdd');

    // 意见反馈类型
    $router->get('/feedbackType', 'Front\FeedbackController@feedbackType');
});

