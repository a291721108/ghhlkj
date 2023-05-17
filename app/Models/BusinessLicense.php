<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessLicense extends Model
{

    protected $table = 'gh_business_license';

    /**
     * type
     */
    const  LICENSE_STATUS_ONE = 0;  // 默认
    const  LICENSE_STATUS_TWO = 1;  // 正常

    const   LICENSE_STATUS_MSG_ARRAY = [
        self::LICENSE_STATUS_ONE      => "默认",
        self::LICENSE_STATUS_TWO      => "正常",
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
