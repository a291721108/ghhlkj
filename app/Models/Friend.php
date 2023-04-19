<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{

    protected $table = 'gh_user_friend';

    /**
     * type
     */
    const  FRIEND_STATUS_ONE = 1;  // 正常
    const  FRIEND_STATUS_TWO = -1;  // 禁用

    const   FRIEND_STATUS_MSG_ARRAY = [
        self::FRIEND_STATUS_ONE      => "正常",
        self::FRIEND_STATUS_TWO      => "禁用",
    ];


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
