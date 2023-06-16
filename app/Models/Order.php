<?php

namespace App\Models;


use Illuminate\Http\Request;

class Order extends Common
{

    protected $table = 'gh_orders';
    public $timestamps = false;

    const ORDER_SYS_TYPE_ZERO = 0;  // 已取消
    const ORDER_SYS_TYPE_ONE = 1;  // 待付款
    const ORDER_SYS_TYPE_TWO = 2;  // 已入住
    const ORDER_SYS_TYPE_THERE = 3;  // 已完成
    const ORDER_SYS_TYPE_FOUR = 4;  // 已预约

    //是否退款
    const ORDER_CHECK_OUT_ZERO = 0;  // 默认
    const ORDER_CHECK_OUT_ONE = 1;  // 已退款
    const ORDER_CHECK_OUT_TWO = 2;  // 待处理

    //是否续费
    const ORDER_RENEW_ZERO = 0;  // 默认
    const ORDER_RENEW_ONE = 1;  // 续费
    const ORDER_RENEW_TWO = 2;  // 待处理

    /**
     * 信息提示
     */
    const   INS_MSG_ARRAY = [
        self::ORDER_SYS_TYPE_ZERO     => "已取消",
        self::ORDER_SYS_TYPE_ONE      => "待付款",
        self::ORDER_SYS_TYPE_TWO      => "已入住",
        self::ORDER_SYS_TYPE_THERE    => "已完成",
        self::ORDER_SYS_TYPE_FOUR     => "已预约",
    ];

    const   CHECK_OUT_MSG_ARRAY = [
        self::ORDER_CHECK_OUT_ZERO     => "默认",
        self::ORDER_CHECK_OUT_ONE      => "已退款",
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
     * 分页
     * @param $query
     * @param $page
     * @param $pageSize
     * @return array
     */
    public function getMsgPageList($query, $page, $pageSize)
    {
        $perPage = $pageSize ?: $query->getPerPage();
        $total   = $query->toBase()->getCountForPagination();
        $results = $query->paginate($pageSize, ['*'], 'page', $page);

        $pages = ceil($total / $perPage);

        return [
            'total'        => $total,
            'current_page' => $page,
            'page_size'    => $perPage,
            'pages'        => $pages,
            'data'         => $results
        ];
    }

    public function refunds()
    {
        return $this->hasOne(OrderRefunds::class, 'order_id')->where('status','0');
    }

    public function renewal()
    {
        return $this->hasOne(OrderRenewal::class, 'order_id');
    }
}
