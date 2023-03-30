<?php

namespace App\Service\Front;

use App\Models\User;
use App\Service\Common\RedisService;
use Illuminate\Support\Facades\Auth;

class AuthService
{
ceshi
    /**
     * 用户登录逻辑
     * @param $request
     */
    public static function login($request)
    {
        $name = $request->name;
        $password = $request->password;

        // 判断用户是否存在
        $useInfo = User::where('name', '=', $name)->first();
        if (!$useInfo) {
            return 'error';
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

        return [
            'api_token' => $token,
            'user_username'  => $useInfo->name,
        ];

    }
}
