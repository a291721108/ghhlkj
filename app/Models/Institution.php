<?php

namespace App\Models;


use Illuminate\Http\Request;

class Institution extends Common
{

    protected $table = 'gh_institution';

    protected $request;

//    public function __construct(Request $request) {
//        $this->request = $request;
//    }
    public $timestamps = true;

    const INSTITUTION_SYS_STATUS_ONE = 1;  // 启用
    const INSTITUTION_SYS_STATUS_TWO = -1;  // 禁用


    const INSTITUTION_SYS_TYPE_ONE = 1;  // 民办
    const INSTITUTION_SYS_TYPE_TWO = 2;  // 政府

    /**
     * 信息提示
     */
    const   INS_MSG_ARRAY = [
        self::INSTITUTION_SYS_STATUS_ONE    => "启用",
        self::INSTITUTION_SYS_STATUS_TWO    => "禁用",
    ];

    const   INS_TYPE_ARRAY = [
        self::INSTITUTION_SYS_TYPE_ONE    => "民办",
        self::INSTITUTION_SYS_TYPE_TWO    => "政府",
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

    /**
     * 关联机构表
     */
    public function products($class = InstitutionHome::class)
    {
        return $this->hasMany($class,'institution_id','id')
            ->select('id', 'institution_id', 'home_type', 'home_img','home_pic','home_size','home_detal','home_facility','status','created_at');
    }

    /**
     * 通过机构id获取详情
     */
    public static function getInstitutionIdByName($id)
    {
        return self::where('id', $id)->get()->toarray();
    }

    /**
     * 通过机构id获取名字
     */
    public static function getInstitutionId($id)
    {
        return self::where('id', $id)->value('institution_name');
    }
}
