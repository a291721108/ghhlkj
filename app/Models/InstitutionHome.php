<?php

namespace App\Models;


class InstitutionHome extends Common
{

    protected $table = 'gh_institution_home';

    public $timestamps = true;

    const Home_SYS_STATUS_ONE = 1;  // 启用
    const Home_SYS_STATUS_TWO = -1;  // 禁用


    /**
     * 信息提示
     */
    const   Home_MSG_ARRAY = [
        self::Home_SYS_STATUS_ONE    => "启用",
        self::Home_SYS_STATUS_TWO    => "禁用",
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
    public static function getHomeListPage($query, $page, $pageSize)
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


    /**
     * 关联机构表
     */
    public function products()
    {
        return $this->hasMany(Institution::class,'institution_id');
//        return $this->hasMany($class, 'institution_id', 'id');
    }


}
