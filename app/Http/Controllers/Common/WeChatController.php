<?php
namespace App\Http\Controllers\Common;

use App\Models\User;
use App\Models\UserWxInfo;
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
     * @method get
     * @url 47.92.82.25/api/auth
     *
     * @header api_token 必选 string api_token放到authorization中
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

    /**
     * @catalog API/微信
     * @title 授权回调
     * @description 授权回调
     * @method get
     * @url 47.92.82.25/api/callback
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param code 必选 string code
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
    public function callback(Request $request)
    {

        $this->validate($request, [
            'code' => 'required',
        ]);

        $config = config('wechat.official_account.default');
        $app = Factory::officialAccount($config);

        // 获取 OAuth 授权结果用户信息
        $oauth = $app->oauth;
        $user = $oauth->userFromCode($request->code);
        $rawUser = $user->getRaw();
        $_SESSION['wechat_user'] = $user->toArray();

        $userInfo = User::getUserInfo();
        $data = [
            'user_id'       => $userInfo->id,
            'openid'	    =>	$rawUser['openid'],//用户在公众号中的唯一标识
            'nickname'      =>	$rawUser['nickname'],//varchar(50)	用户昵称
            'sex'           =>	$rawUser['sex'],	//用户性别(0-未知，1-男，2-女)
            'province'      =>	$rawUser['province'],	//用户所在省份
            'city'          =>	$rawUser['city'],	//用户所在城市
            'country'	    =>	$rawUser['country'],//用户所在国家
            'headimgurl'    =>	$rawUser['headimgurl'],	//用户头像URL
            'wx_status'     => UserWxInfo::WX_USER_INFO_ONE,
            'created_at'    =>	time(),	//创建时间
        ];

        if (UserWxInfo::insert($data)){
            return $this->success('authorizationSucceeds', '200', []);
        }

        return $this->error('authorizationSucceeds');
    }

    /**
     * @catalog API/微信
     * @title 授权
     * @description 授权
     * @method post
     * @url 47.92.82.25/api/wechatAuthorization
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param code 必选 string code
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
    public function wechatAuthorization(Request $request)
    {
        $this->validate($request, [
            'openid'    => 'required',
            'unionid'   => 'required',
            'nickname'  => 'required',
            'sex'       => 'required',
//            'province'  => 'required',
//            'city'      => 'required',
//            'country'   => 'required',
            'headimgurl' => 'required',
        ]);

        $userInfo = User::getUserInfo();
        $data = [
            'user_id'       =>  $userInfo->id,
            'openid'	    =>	$request->openid,//用户在公众号中的唯一标识
            'unionid'       =>  $request->unionid,
            'nickname'      =>	$request->nickname,//varchar(50)	用户昵称
            'sex'           =>	$request->sex,	//用户性别(0-未知，1-男，2-女)
            'province'      =>	$request->province,	//用户所在省份
            'city'          =>	$request->city,	//用户所在城市
            'country'	    =>	$request->country,//用户所在国家
            'headimgurl'    =>	$request->headimgurl,	//用户头像URL
            'wx_status'     => UserWxInfo::WX_USER_INFO_ONE,
            'created_at'    =>	time(),	//创建时间
        ];

        if (UserWxInfo::insert($data)){
            return $this->success('authorizationSucceeds', '200', []);
        }

        return $this->error('authorizationSucceeds');
    }
}
