<?php
/** @var Router $router */
use Illuminate\Http\Request;
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
    $router->get('/', function (Request $request) {
        // 验证服务器地址的有效性
        $signature = $request->input('signature');
        $timestamp = $request->input('timestamp');
        $nonce = $request->input('nonce');
        $token = "ghhlkj2023";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return $request->input('echostr');
        }
    });


    $router->post('/', function (Request $request) {
        $postStr = $request->getContent();
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $msgType = trim($postObj->MsgType);
            switch ($msgType) {
                case "event":
                    // 处理事件消息
                    break;
                case "text":
                    // 处理文本消息
                    break;
                default:
                    // 其他类型的消息
                    break;
            }
        }
    });

    // 登录
    $router->post('/login', 'Front\AuthController@login');

    // 发送短信验证吗
    $router->post('/sendSms', 'Common\SendMsgController@sendSms');

    // 忘记密码
    $router->post('/forgotPassword', 'Front\AuthController@forgotPassword');

    //验证码登录
    $router->post('/sendSmsLogin', 'Front\AuthController@sendSmsLogin');

    // 手机号验证
    $router->post('/validateTel', 'Front\AuthController@validateTel');

    //用户信息修改
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

    // 微信授权
    $router->get('/auth', 'Common\WeChatController@auth');

    // 授权回调
    $router->get('/callback', 'Common\WeChatController@callback');

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

    // 获取用户基本信息
    $router->post('/getInfo', 'Front\AuthController@getInfo');

    // 用户基本信息修改
    $router->post('/upInfo', 'Front\AuthController@upInfo');

    // 修改手机号
    $router->post('/upTel', 'Front\AuthController@upTel');

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
    $router->post('/agencyAppointment', 'Front\BookingController@agencyAppointment');

    // 预约列表
    $router->get('/reservationList', 'Front\BookingController@reservationList');

    // 获取单条预约信息
    $router->get('/getBookOneMsg', 'Front\BookingController@getBookOneMsg');

    // 获取订单详情
    $router->post('/userReservationRecord', 'Front\OrderController@userReservationRecord');

    // 下单
    $router->post('/placeAnOrder', 'Front\OrderController@placeAnOrder');

    // 订单列表
    $router->post('/orderList', 'Front\OrderController@orderList');

    // 订单列表
    $router->post('/orderDel', 'Front\OrderController@orderDel');

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

    // 获取用户意见反馈
    $router->post('/getFeedbackList', 'Front\FeedbackController@getFeedbackList');

    // 反馈详情
    $router->post('/FeedbackList', 'Front\FeedbackController@FeedbackList');

    // 授权回调
    $router->post('/wechatAuthorization', 'Common\WeChatController@wechatAuthorization');

    // 查询用户是否设置支付密码
    $router->post('/getPayPassword', 'Front\AuthController@getPayPassword');

    // 设置支付密码
    $router->post('/setPayPassword', 'Front\AuthController@setPayPassword');

    // 验证支付密码
    $router->post('/upPayPassword', 'Front\AuthController@upPayPassword');

    // 修改支付密码
    $router->post('/validateCard', 'Front\AuthController@validateCard');
});

