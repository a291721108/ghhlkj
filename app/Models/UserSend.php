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
