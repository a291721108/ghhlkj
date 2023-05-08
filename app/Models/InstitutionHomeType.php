<?php

namespace App\Models;


class InstitutionHomeType extends Common
{

    protected $table = 'gh_institution_type';

    public $timestamps = true;

    const Home_TYPE_SYS_STATUS_ONE = 1;  // 启用
    const Home_TYPE_SYS_STATUS_TWO = -1;  // 禁用


    /**
     * 信息提示
     */
    const   Home_MSG_ARRAY = [
        self::Home_TYPE_SYS_STATUS_ONE => "正常",
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
        return self::where('institution_id', $homeTpyeId)->where('status', '>', self::Home_TYPE_SYS_STATUS_TWO)->select('id','home_type','home_price','home_detail')->get()->toArray();
    }


    /**
     * 通过机构id获取该机构下最便宜的房间
     */
    public static function getInstitutionIdByPrice($id)
    {
        return self::where('institution_id', $id)->where('status','>',self::Home_TYPE_SYS_STATUS_TWO)->min('home_price');
    }

    /**
     * 通过机构id获取该机构下的类型
     */
    public static function getInstitutionIdByName($id)
    {
        return self::where('id', $id)->select('id','home_type','home_price','home_detail','home_img')->get()->toArray();
    }

}
