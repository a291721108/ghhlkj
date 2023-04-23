<?php
namespace App\Http\Controllers\Common;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Overtrue\LaravelWeChat\Events\WeChatUserAuthorized;
use Overtrue\LaravelWeChat\Facade as EasyWeChat;

class WeChatController extends BaseController
{

    public function auth(Request $request)
    {
        $config = config('wechat.official_account.default');
        $app = Factory::officialAccount($config);
        $response = $app->oauth->scopes('snsapi_userinfo')->redirect('http://47.92.82.25/api/callback');
        return $response;
    }

    public function callback()
    {
        $config = config('wechat.official_account.default');

        $app = Factory::officialAccount($config);
        $oauth = $app->oauth;

        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();
        $openid = $user->getId();
        $userInfo = $user->getOriginal();

        dd($user);
        // 这里可以将用户信息存入数据库或者做其他操作
        // ...

        return '授权成功';
    }

}
