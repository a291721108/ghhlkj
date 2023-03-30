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
        'The user does not exist.' => "用户不存在",
        'login_success'             => "登录成功",

        'Unauthorized'              => "未授权"
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
