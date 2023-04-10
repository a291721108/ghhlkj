<?php

namespace App\Service\Front;

use App\Events\FrontLoginEvent;
use App\Models\User;
use App\Models\UserSend;
use App\Service\Common\RedisService;
use Illuminate\Support\Facades\Auth;
use StdClass;
use function Symfony\Component\String\u;

class AuthService
{

    /**
     * 用户登录逻辑
     * @param $request
     */
    public static function login($request)
    {
        $phone = $request->phone;
        $password = $request->password;

        // 判断用户是否存在
        $useInfo = User::where('phone', '=', $phone)->where('status','=',User::USER_STATUS_ONE)->first();
        if (!$useInfo) {
            return 'The user does not exist.';
        }

        // 判断用户秘密是否正确
        $password = md5($password) . $useInfo->salt;
        if ($password !== $useInfo->password . $useInfo->salt) {
            return 'password_error';
        }

        //  登录成功 为用户颁发token
        $token = Auth::guard('api')->login($useInfo);

        // 将token存在redis中 过期时间设置为1天
        $key = "gh_user_front_token_" . $useInfo->id;
        RedisService::set($key, $token);

        // redis存入异常抛错
        if (!RedisService::get($key)) {
            return 'redis_write_token_error';
        }

        // 时间监听 处理登录完成之后的逻辑
        $obj             = new StdClass();
        $obj->id         = $useInfo->id;
        event(new FrontLoginEvent($obj));

        return [
            'api_token'         => $token,
            'user_id'           => $useInfo->id,
            'user_username'     => $useInfo->name,
            'user_img'          => $useInfo->img,
            'user_email'        => $useInfo->email,
            'user_address'      => $useInfo->address,
            'user_tel'          => $useInfo->phone,
            'user_gender'       => User::GENDER_MSG_ARRAY[$useInfo->gender],
            'user_birthday'     => ytdTampTime($useInfo->birthday),
        ];

    }

    /**
     * 用户信息修改
     * @param $request
     */
    public static function register($request)
    {

        $phone = $request->phone;

        // 判断用户是否存在
        $useInfo = User::where('phone', '=', $phone)->first();
        $useInfo->name =$request->name;
        $useInfo->password = $request->password;
        $useInfo->salt = rand(1, 100);
        $useInfo->img = $request->img;
        $useInfo->email = $request->email;
        $useInfo->created_at = time();

        return $useInfo->save();

    }

    /**
     * 忘记密码
     * @return string|bool
     */
    public function forgotPassword($request)
    {
        $code      = $request->dxcodess;
        $phone     = $request->phone;
        $passwords = $request->passwords;

        // 判断是否有验证吗
        $sendInfo = UserSend::where('phone', '=', $phone)->orderBy('id', 'desc')->first();

        if (!$sendInfo) {
            return 'phone_error';
        }

        //  验证吗是否过期 有效期限五分钟
        if (time() >= ($sendInfo->send_time + 300)) {
            return 'code_expired';
        }

        // 验证码错误
        if ($sendInfo->code !== intval($code)) {
            return 'code_error';
        }

        $useInfo                = User::where('user_tel', '=', $phone)->first();
        $useInfo->user_password = md5($passwords);
        $useInfo->salt          = rand(1, 100);


        if ($useInfo->save()) {
            return 'success';
        }

        return 'error';
    }

    /**
     * 验证码登录
     * @return array
     */
    public static function sendSmsLogin($request)
    {
        $code      = $request->dxcodess;
        $phone     = $request->phone;

        // 判断是否有验证吗
        $sendInfo = UserSend::where('phone', '=', $phone)->orderBy('id', 'desc')->first();

        if (!$sendInfo) {
            return 'phone_error';
        }

        //  验证吗是否过期 有效期限五分钟
        if (time() >= ($sendInfo->send_time + 300)) {
            return 'code_expired';
        }

        // 验证码错误
        if ($sendInfo->code !== intval($code)) {
            return 'code_error';
        }

        // 判断用户是否存在
        $useInfo = User::where('phone', '=', $phone)->where('status','=',User::USER_STATUS_ONE)->first();

        if (!$useInfo) {

            //如果没有这个手机号插入数据库
            $data = [
                'name'          => "游客111",
                'phone'         => $phone,
                'status'        => User::USER_STATUS_ONE,
                'created_at'    => time()
            ];

            User::insert($data);
            return 'New user login';
        }
        if (!$useInfo->password){
            return "passwordNull";
        }
        //  登录成功 为用户颁发token
        $token = Auth::guard('api')->login($useInfo);

        // 将token存在redis中 过期时间设置为1天
        $key = "gh_user_front_token_" . $useInfo->id;
        RedisService::set($key, $token);

        return [
            'api_token'         => $token,
            'user_id'           => $useInfo->id,
            'user_username'     => $useInfo->name,
            'user_img'          => $useInfo->img,
            'user_email'        => $useInfo->email ?? '',
            'user_address'      => $useInfo->address ?? '',
            'user_tel'          => $useInfo->phone,
            'user_gender'       => User::GENDER_MSG_ARRAY[$useInfo->gender] ?? '',
            'user_birthday'     => ytdTampTime($useInfo->birthday) ?? '',
        ];
    }

    /**
     * 注销账户
     * @return string|bool
     */
    public static function closeAnAccount()
    {
        $userInfo = User::getUserInfo();
        $userInfo->status = User::USER_STATUS_TWO;
        $userInfo->save();

        if ($userInfo){
            return 'success';
        }
        return 'error';
    }

    /**
     * 安全退出
     * @return string|bool
     */
    public static function safeWithdrawing()
    {
        $user = User::getUserInfo();
        $userInfo = User::logout();
        // 将token存在redis中 过期时间设置为1天
        RedisService::del('gh_user_front_token_' . $user->id);
        if (!$userInfo){
            return "safe withdrawing";
        }
        return "error";
    }
}
