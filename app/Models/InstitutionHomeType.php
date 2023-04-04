<?php

namespace App\Models;


class InstitutionHomeType extends Common
{

    protected $table = 'gh_institution_home_type';

    public $timestamps = true;

    const Home_TYPE_SYS_STATUS_ONE = 1;  // 启用
    const Home_TYPE_SYS_STATUS_TWO = -1;  // 禁用


    /**
     * 信息提示
     */
    const   Home_MSG_ARRAY = [
        self::Home_TYPE_SYS_STATUS_ONE => "启用",
        self::Home_TYPE_SYS_STATUS_TWO => "禁用",
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
     * 根据id获取房间类型名称
     * @param $homeTpyeId
     */
    public static function getHomeTypeName($homeTpyeId)
    {
        return self::where('id', $homeTpyeId)->value('home_type_name');
    }

}
