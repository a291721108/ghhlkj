<?php
namespace App\Http\Controllers\Common;

use EasyWeChat\Factory;
use Illuminate\Http\Request;

class WeChatController extends BaseController
{

    public function auth(Request $request)
    {
        $token = 'Ghhlkj2023'; // 在公众号服务器配置中设置的 token

        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $echostr = $_GET['echostr'];

        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr === $signature) {
            // 验证通过，输出 echostr
            echo $echostr;
        } else {
            // 验证失败
            echo 'Token verification failed';
        }

//        // 读取配置文件中的微信公众号相关配置
//        $config = config('wechat.official_account.default');
//        //        $config = [
//        //            'app_id' => env('WECHAT_APPID'),
//        //            'secret' => env('WECHAT_SECRET'),
//        //            'response_type' => 'array',
//        //            'oauth' => [
//        //                'scopes' => ['snsapi_userinfo'],
//        //                'callback' => env('WECHAT_OAUTH_CALLBACK'),
//        //            ],
//        //        ];
//
//        // 创建微信 SDK 对象
//        $app = Factory::officialAccount($config);
//
//        // 获取微信授权登录 URL
//        $url = $app->oauth->redirect();
//
//        // 将 URL 返回给前端页面
//        return response()->json(['url' => $url]);
    }

    public function callback()
    {
        $config = config('wechat.official_account.default');
        // 获取授权用户信息
//        $config = [
//            'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_APPID'),
//            'secret' => env('WECHAT_OFFICIAL_ACCOUNT_SECRET'),
//            'oauth' => [
//                'scopes'   => ['snsapi_userinfo'],
//                'callback' => url('/wechat/callback'),
//            ],
//        ];

        $app = Factory::officialAccount($config);
        $oauth = $app->oauth;

        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();
        $openid = $user->getId();
        $userInfo = $user->getOriginal();

        // 这里可以将用户信息存入数据库或者做其他操作
        // ...

        return '授权成功';
    }

}
