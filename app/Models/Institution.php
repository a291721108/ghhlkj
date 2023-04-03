<?php

namespace App\Models;


class Institution extends Common
{

    protected $table = 'gh_institution';

    public $timestamps = true;

    const INSTITUTION_SYS_STATUS_ONE = 1;  // 启用
    const INSTITUTION_SYS_STATUS_TWO = -1;  // 禁用


    /**
     * 信息提示
     */
    const   INS_MSG_ARRAY = [
        self::INSTITUTION_SYS_STATUS_ONE    => "启用",
        self::INSTITUTION_SYS_STATUS_TWO    => "禁用",
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
    public static function getProListPage($query, $page, $pageSize)
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
