<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Common\BaseController;
use App\Service\Front\AuthService;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    /**
     * @catalog app端/用户相关
     * @title 用户登录
     * @description 用户登录的接口
     * @method post
     * @url 47.92.82.25/api/login
     *
     * @param phone 必选 string 手机号(17821211068)
     * @param password 必选 string 用户密码(6-12数字加字母组成)(admin)
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
            'phone'         => 'required|numeric',
            'password'      => 'required'
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
     * @title 用户信息修改
     * @description 用户信息修改
     * @method post
     * @url 47.92.82.25/api/register
     *
     * @param name 必选 string 名字
     * @param password 必选 string 用户密码(6-12数字加字母组成)
     * @param phone 必选 int 手机号
     * @param img 非必选 string 图片
     * @param email 非必选 string 邮箱
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
            'password'   => 'required',
            'phone'     => 'required|numeric',
        ]);

        $res = AuthService::register($request);

        if (is_bool($res)){
            return $this->success('success');
        }

        return $this->error($res);
    }

    /**
     * @catalog app端/用户相关
     * @title 忘记密码
     * @description 忘记密码接口
     * @method post
     * @url 47.92.82.25/api/forgotPassword
     *
     * @param dxcodess 必选 int 验证吗
     * @param phone 必选 string 手机号
     * @param passwords 必选 string 密码
     *
     * @return {"code":200,"msg":"修改密码成功","data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param data array 返回数据
     *
     * @remark
     * @number 2
     */
    public function forgotPassword(Request $request)
    {

        $this->validate($request, [
            'dxcodess'  => 'required',
            'phone'     => 'required',
            'passwords' => 'required'
        ]);

        // 验证手机号格式
        if (!validatePhone($request->phone)) {
            return $this->error('phone_error');
        }

        // 密码格式 6-12位 字符加数字组合
        if (strlen($request->passwords) < 6 || strlen($request->passwords) > 12) {
            return $this->error('password_length_error');
        }

        $res = AuthService::forgotPassword($request);

        if ($res == 'success') {
            return $this->success('update_true');
        }

        return $this->error($res);
    }

    /**
     * @catalog app端/用户相关
     * @title 验证码登录
     * @description 用户验证码登录
     * @method post
     * @url 47.92.82.25/api/sendSmsLogin
     *
     * @param phone 必选 string 手机号
     * @param dxcodess 必选 int 验证码(6位数字加字母组成)
     *
     * @return {"meta":{"status":200,"msg":"登录成功"},"data":{"api_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80Ny45Mi44Mi4yNVwvYXBpXC9sb2dpbiIsImlhdCI6MTY4MDE0ODU1NiwiZXhwIjoxNjgxNDQ0NTU2LCJuYmYiOjE2ODAxNDg1NTYsImp0aSI6ImNoVXhHcHBpT2xDYlN6OXciLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.DC_a1lX4rxy4h3o5ufE6QPqPRod4nYENBtMDELyWTts","user_username":"1"}}
     *
     * @return_param api_token string api_token
     * @return_param name string 姓名
     *
     * @remark
     * @number 1
     */
    public function sendSmsLogin(Request $request)
    {

        $this->validate($request, [
            'phone'     => 'required|numeric',
            'dxcodess'  => 'required|numeric',
        ]);

        $res = AuthService::sendSmsLogin($request);

        if ($res) {
            return $this->success('login_success', 200, $res);
        }

        return $this->error($res);
    }
}
