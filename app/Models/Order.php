<?php

namespace App\Models;


use Illuminate\Http\Request;

class Order extends Common
{

    protected $table = 'gh_orders';
    public $timestamps = true;

    const ORDER_SYS_TYPE_ZERO = -1;  // 删除
    const ORDER_SYS_TYPE_ONE = 1;  // 待付款
    const ORDER_SYS_TYPE_TWO = 2;  // 已入住
    const ORDER_SYS_TYPE_THERE = 3;  // 已完成



    /**
     * 信息提示
     */
    const   INS_MSG_ARRAY = [
        self::ORDER_SYS_TYPE_ZERO     => "删除",
        self::ORDER_SYS_TYPE_ONE      => "待付款",
        self::ORDER_SYS_TYPE_TWO      => "已入住",
        self::ORDER_SYS_TYPE_THERE    => "已完成",
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
     * @param $page
     * @param $pageSize
     * @return array
     */
    public function getMsgPageList($page, $pageSize, $field = ['*'], $where = []): array
    {
        return $this->paginate($pageSize, $field, $page, 'page', $where);
    }
}
