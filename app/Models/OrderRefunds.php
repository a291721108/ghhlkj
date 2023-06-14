<?php

namespace App\Models;


use Illuminate\Http\Request;

class OrderRefunds extends Common
{

    protected $table = 'gh_orders_refunds';
    public $timestamps = false;

    //是否退款
    const ORDER_CHECK_OUT_ZERO = 0;  // 默认
    const ORDER_CHECK_OUT_ONE = 1;  // 已退款
    const ORDER_CHECK_OUT_TWO = 2;  // 拒绝
    const ORDER_CHECK_OUT_THREE = 3;  // 取消

    /**
     * 信息提示
     */
    const   CHECK_OUT_MSG_ARRAY = [
        self::ORDER_CHECK_OUT_ZERO      => "默认",
        self::ORDER_CHECK_OUT_ONE       => "已退款",
        self::ORDER_CHECK_OUT_TWO       => "拒绝",
        self::ORDER_CHECK_OUT_THREE      => "取消",
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
