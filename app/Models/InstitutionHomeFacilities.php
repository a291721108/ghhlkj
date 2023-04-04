<?php

namespace App\Models;


class InstitutionHomeFacilities extends Common
{

    protected $table = 'gh_institution_home_facility';

    public $timestamps = true;

    const Home_FACILITY_STATUS_ONE = 1;  // 启用
    const Home_FACILITY_STATUS_TWO = -1;  // 禁用


    /**
     * 信息提示
     */
    const   Home_MSG_ARRAY = [
        self::Home_FACILITY_STATUS_ONE => "启用",
        self::Home_FACILITY_STATUS_TWO => "禁用",
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
     * 获取房间类型名称
     * @param $homeTpyeId
     */
    public static function getHomeFaclitiyName($homeFaclitiyId)
    {
        return self::where('id', $homeFaclitiyId)->value('hotel_facilities');
    }
}
