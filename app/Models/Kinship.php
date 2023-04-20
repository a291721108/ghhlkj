<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kinship extends Model
{

    protected $table = 'gh_user_kinship';

    /**
     * type
     */
    const  KINSHIP_TYPE_ONE = 1;  // 正常
    const  KINSHIP_TYPE_TWO = -1;  // 禁用

    const   KINSHIP_TYPE_MSG_ARRAY = [
        self::KINSHIP_TYPE_ONE      => "正常",
        self::KINSHIP_TYPE_TWO      => "禁用",
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
     * 通过id获取亲友类型
     */
    public static function getIdByname($id)
    {
        return self::where('id',$id)->select('id','kinship_name')->get()->toArray();
    }

}
