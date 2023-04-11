<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExt extends Model
{

    protected $table = 'gh_user_ext';


    /**
     * 获取用户基本信息
     * @param $name
     */
    public static function getMsgByUserId($id)
    {
        return self::where('user_id', $id)->value('user_age');
    }


}
