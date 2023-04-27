<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSend extends Model
{

    protected $table = 'gh_user_send';

    /**
     * type
     */
    const  USER_SEND_ONE = 1;  // 登录
    const  USER_SEND_TWO = 2;  // 密码修改
    const  USER_SEND_THERE = 3;  // 亲友绑定
    const  USER_SEND_FOUR = 4;  // 体现验证
    const  USER_SEND_FIVE = 5;  // 重新实名认证
    const  USER_SEND_SIX = 6;  // 身份验证


    /**
     * 格式化时间
     * @param $value
     * @return false|int|string|null
     */
    public function fromDateTime($value)
    {
        return strtotime(parent::fromDateTime($value));
    }


}
