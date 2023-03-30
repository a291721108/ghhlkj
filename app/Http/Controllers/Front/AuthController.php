<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Common\BaseController;
use App\Service\Front\AuthService;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    /**
     * @catalog 后台/用户相关
     * @title 用户登录
     * @description 用户登录的接口
     * @method post
     * @url 47.92.82.25/api/login
     *
     * @param name 必选 string 名字
     * @param password 必选 string 用户密码(6-12数字加字母组成)
     *
     * @return {"meta":{"status":200,"msg":"登录成功"},"data":{"api_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80Ny45Mi44Mi4yNVwvYXBpXC9sb2dpbiIsImlhdCI6MTY4MDE0ODU1NiwiZXhwIjoxNjgxNDQ0NTU2LCJuYmYiOjE2ODAxNDg1NTYsImp0aSI6ImNoVXhHcHBpT2xDYlN6OXciLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.DC_a1lX4rxy4h3o5ufE6QPqPRod4nYENBtMDELyWTts","user_username":"1"}}
     *
     * @return_param api_token string api_token
     * @return_param name string 姓名
     *
     * @remark
     * @number 1
     */
    public function login(Request $request)
    {
        // 数据校验
        $this->validate($request, [
            'name'      => 'required',
            'password' => 'required'
        ]);

        $res = AuthService::login($request);

        if (is_array($res)) {
            // 登入成功
            return $this->success('login_success', 200, $res);
        }

        return $this->error($res);
    }

    /**
     * @catalog app端/用户相关
     * @title 用户注册
     * @description 用户注册的接口
     * @method post
     * @url 47.92.82.25/api/register
     *
     * @param name 必选 string 名字
     * @param password 必选 string 用户密码(6-12数字加字母组成)
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param
     *
     * @remark
     * @number 1
     */
    public function register(Request $request)
    {
        // 数据校验
        $this->validate($request, [
            'name'      => 'required',
            'password' => 'required'
        ]);

        $res = AuthService::register($request);

        if (is_bool($res)){
            return $this->success('success');
        }

        return $this->error($res);
    }
}
