<?php

namespace App\Models;


use Illuminate\Http\Request;

class OrderRenewal extends Common
{

    protected $table = 'gh_order_renewal';
    public $timestamps = false;

    //是否退款
    const ORDER_RENEWAL_ZERO = 0;  // 默认
    const ORDER_RENEWAL_ONE = 1;  // 同意
    const ORDER_RENEWAL_TWO = 2;  // 拒绝

    /**
     * 信息提示
     */
    const   CHECK_OUT_MSG_ARRAY = [
        self::ORDER_RENEWAL_ZERO     => "默认",
        self::ORDER_RENEWAL_ONE      => "同意",
        self::ORDER_RENEWAL_TWO      => "拒绝",
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
