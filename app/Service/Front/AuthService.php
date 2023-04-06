<?php

namespace App\Service\Front;

use App\Events\FrontLoginEvent;
use App\Models\User;
use App\Service\Common\RedisService;
use Illuminate\Support\Facades\Auth;
use StdClass;

class AuthService
{
ceshi
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
        $key = "oa_user_front_token_" . $useInfo->id;
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
            'user_email'        => $useInfo->email,
            'user_address'      => $useInfo->address,
            'user_img'          => $useInfo->img,
            'user_tel'          => $useInfo->phone,
            'user_car'          => $useInfo->car,
            'user_gender'       => User::GENDER_MSG_ARRAY[$useInfo->gender],
        ];

    }

    /**
     * 用户注册
     * @param $request
     */
    public static function register($request)
    {
        $name = $request->name;

        // 判断用户是否存在
        $useInfo = User::where('name', '=', $name)->first();
        if ($useInfo) {
            return 'User already exists.';
        }

        $data = [
            'name'          => $request->name,
            'password'      => md5($request->password),
            'salt'          => rand(1, 100),
            'created_at'        => time(),
        ];

        return User::insert($data);

    }


}
