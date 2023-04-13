<?php

namespace App\Models;


use Illuminate\Http\Request;

class Booking extends Common
{

    protected $table = 'gh_bookings';


    public $timestamps = true;

    const BOOKING_SYS_TYPE_ONE = 1;  // 成功

    const BOOKING_SYS_TYPE_TWO = 2;  // 取消
    const BOOKING_SYS_TYPE_THERE = 3;  // 已过期
    const BOOKING_SYS_TYPE_FOUR = -1;  // 删除

    /**
     * 信息提示
     */
    const   INS_MSG_ARRAY = [
        self::BOOKING_SYS_TYPE_ONE      => "成功",
        self::BOOKING_SYS_TYPE_TWO      => "取消",
        self::BOOKING_SYS_TYPE_THERE    => "已过期",
        self::BOOKING_SYS_TYPE_FOUR     => "删除",
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
