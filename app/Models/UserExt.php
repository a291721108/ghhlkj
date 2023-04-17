<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExt extends Model
{

    protected $table = 'gh_user_ext';


    /**
     * 认证状态
     * @params status 认证状态，0表示未认证，1表示已认证
     * @params result 认证结果，0表示认证未通过，1表示认证通过
     */
    const  USER_STATUS_ONE = 0;  // 未认证
    const  USER_STATUS_TWO = 1;  // 已认证


    const  USER_RESULT_ONE = 0;  // 未通过
    const  USER_RESULT_TWO = 1;  // 认证通过


    const   USER_STATUS_MSG_ARRAY = [
        self::USER_STATUS_ONE      => "未认证",
        self::USER_STATUS_TWO      => "已认证",
    ];

    const   USER_RESULT_MSG_ARRAY = [
        self::USER_RESULT_ONE      => "未通过",
        self::USER_RESULT_TWO      => "认证通过",
    ];

    /**
     * 获取用户基本信息
     * @param $name
     */
    public static function getMsgByUserId($id)
    {
        return self::where('user_id', $id)->select('id_name','id_type','id_number', 'status', 'result')->get()->toArray();
    }

}
