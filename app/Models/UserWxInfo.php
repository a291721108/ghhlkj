<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWxInfo extends Model
{

    protected $table = 'gh_wx_auth_info';

    /**
     * type
     */
    const  WX_USER_INFO_ZERO = 0;  // 未授权
    const  WX_USER_INFO_ONE = 1;  // 以授权

    const   WX_USER_MSG_ARRAY = [
        self::WX_USER_INFO_ZERO      => "未授权",
        self::WX_USER_INFO_ONE       => "以授权",
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

    /**
     * 通过id获取用户微信信息
     */
    public static function getIdByWxInfo($id)
    {
        return self::where('user_id',$id)->value('wx_status');
    }
}
