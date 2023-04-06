<?php

namespace App\Exceptions;


class ErrorCode
{


    /**
     * 错误数组
     * @var array|string[]
     */
    public  $errorCode = [
        'success'                   => "成功",
        'error'                     => "失败",
        'The user does not exist.'  => "用户不存在",
        'User already exists.'      => "用户已存在",
        'password_error'            => "密码错误",
        'password_length_error'     => "密码格式错误,请输入6-12位字符密码",
        'password_style_error'      => "密码格式不正确",
        'update_true'               => "修改密码成功",
        'login_success'             => "登录成功",
        'New user login'            => "新用户登录",

        'code_send_success'         => "验证码发送成功",
        'code_expired'              => "验证吗已过期",
        'code_error'                => "验证吗输入错误",
        'code_has_time'             => "验证吗任在有效期内，请稍后在试",
        'code_send_error'           => "验证码发送失败",




        'Unauthorized'              => "未授权",


    ];

    /**
     * 返回错误
     * @param $code
     * @return mixed|string
     */
    public function getErrorMsg($code)
    {
        $code == 'false' ?? $code = 'error';

        return $this->errorCode[$code];
    }


    /**
     * 返回成功
     * @param $code
     * @return mixed|string
     */
    public function getSuccessMsg($code)
    {
        $code == 'true' ?? $code = 'success';
        return $this->errorCode[$code];
    }

}
