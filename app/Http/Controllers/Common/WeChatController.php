<?php
namespace App\Http\Controllers\Common;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Overtrue\LaravelWeChat\Events\WeChatUserAuthorized;
use Overtrue\LaravelWeChat\Facade as EasyWeChat;

class WeChatController extends BaseController
{

    /**
     * @catalog API/微信
     * @title 微信授权
     * @description 微信授权
     * @method post
     * @url 47.92.82.25/api/auth
     *
     * @return {"code":200,"msg":"成功","data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param data array 返回数据
     *
     * @remark
     * @number 6
     */
    public function auth(Request $request)
    {

        $config = config('wechat.official_account.default');
        $app = Factory::officialAccount($config);
        $response = $app->oauth->scopes(array(env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_SCOPES')))->redirect(env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_CALLBACK'));

//        return redirect($response, 302, ['Cache-Control' => 'no-cache']);
        return $response;

    }

    public function callback(Request $request)
    {



        $config = config('wechat.official_account.default');
        $app = Factory::officialAccount($config);

        // 获取 OAuth 授权结果用户信息
        $oauth = $app->oauth;
        // 获取 OAuth 授权结果用户信息
        $code = "微信回调URL携带的 code";
        $user = $oauth->userFromCode();

        $_SESSION['wechat_user'] = $user->toArray();

        dd($user);
        // 这里可以将用户信息存入数据库或者做其他操作
        // ...

        return '授权成功';
    }
}
