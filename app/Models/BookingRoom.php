<?php

namespace App\Models;


use Illuminate\Http\Request;

class BookingRoom extends Common
{

    protected $table = 'gh_hotelroom_orderinfo';


    public $timestamps = true;

    const ROOM_SYS_TYPE_ZERO = 0;  // 取消
    const ROOM_SYS_TYPE_ONE = 1;  // 提交订单
    const ROOM_SYS_TYPE_TWO = 2;  // 订房成功
    /**
     * 信息提示
     */
    const   INS_MSG_ARRAY = [
        self::ROOM_SYS_TYPE_ZERO     => "取消",
        self::ROOM_SYS_TYPE_ONE      => "提交订单",
        self::ROOM_SYS_TYPE_TWO      => "订房成功",
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
    public function getInsListPage($query, $page, $pageSize)
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
}
