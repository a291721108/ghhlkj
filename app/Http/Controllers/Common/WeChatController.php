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
        $response = $app->oauth->scopes(array(env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_SCOPES')))->redirect(env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_CALLBACK'));
        return $response;
    }

    public function callback()
    {
        $config = config('wechat.official_account.default');

        $app = Factory::officialAccount($config);

        // 获取 OAuth 授权结果用户信息
        $oauth = $app->oauth;
        dd($app);

        $user = $oauth->user();


        dd($user);
        // 这里可以将用户信息存入数据库或者做其他操作
        // ...

        return '授权成功';
    }

}
