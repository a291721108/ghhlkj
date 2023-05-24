<?php

namespace App\Service\Front;

use App\Models\Institution;
use App\Models\InstitutionHome;
use App\Models\InstitutionHomeFacilities;
use App\Models\InstitutionHomeType;
use App\Service\Common\FunService;
use Illuminate\Support\Facades\DB;

class OrganizationService
{
    /**
     * @param $request
     * @return array
     */
    public static function organizationList($request): array
    {
        $page     = $request->page ?? 1;
        $pageSize = $request->page_size ?? 20;

        $query    = self::makeSearchWhere($request);

        // 获取分页数据
        $result = (new Institution())->getInsListPage($query, $page, $pageSize);

        // 处理特殊字段
        $result['data'] = self::dealReturnData($result['data']);

        return $result;
    }

    /**
     * @param $request
     * @return mixed
     */
    protected static function makeSearchWhere($request)
    {
        $query = Institution::where('gh_institution.status','>',Institution::INSTITUTION_SYS_STATUS_TWO);

        if ($request->institution_serarch) {
            $query->where('institution_name', 'like', "%" . $request->institution_serarch . "%");
        }

        if ($request->institution_address) {
            $query->where('institution_address', 'like', "%" . $request->institution_address . "%");
        }

        if ($request->page_view) {
            $query->orderBy('page_view', 'desc');
        }

        if ($request->price_serarch){
            $query->leftJoin('gh_institution_type AS h', 'gh_institution.id', '=', 'h.institution_id')
                ->select('gh_institution.*', DB::raw('MIN(h.home_price) AS home_price'))
                ->orderBy('home_price', 'asc')
                ->groupBy('gh_institution.id');

        }

        return $query;
    }

    /**
     * 处理数组
     * @param $query
     * @param array $data
     * @return array
     */
    public static function dealReturnData($query, array $data = [])
    {

        foreach ($query as $k => $v) {

            // 处理回参
            $data[$k] = [
                'id'                        => $v['id'],
                'institution_name'          => $v['institution_name'],
                'institution_address'       => $v['institution_address'],
                'institution_img'           => explode(",", $v['institution_img'])[0],
                'price'                     => InstitutionHomeType::getInstitutionIdByPrice($v['id']),
                'status'                    => Institution::INS_MSG_ARRAY[$v['status']]??"",
                'page_view'                 => $v['page_view'],
                'created_at'                => date('Y-m-d H:i', strtotime($v['created_at']))
            ];
        }

        return $data;
    }

    /**
     * 获取机构浏览量前五
     */
    public static function tissueCount()
    {

        // 查询列表
        $query = Institution::where('status', '>', Institution::INSTITUTION_SYS_STATUS_TWO)
            ->orderBy('page_view','desc')
            ->get();

        $data = [];
        foreach ($query as $k => $v) {
            // 处理回参
            $data[$k] = [
                'id'                        => $v['id'],
                'institution_name'          => $v['institution_name'],
                'institution_address'       => $v['institution_address'],
                'institution_img'           => $v['institution_img'],
                'institution_detail'        => $v['institution_detail'],
                'institution_tel'           => $v['institution_tel'],
                'institution_type'          => $v['institution_type'],
                'page_view'                 => $v['page_view'],
                'status'                    => $v['status'],
                'created_at'                => date('Y-m-d H:i', strtotime($v['created_at']))
            ];
        }
        return $data;
    }

    /**
     * 通过id获取机构详情列表
     */
    public static function organizationDetails($request)
    {
        // 查询列表

        $query = Institution::where('id',$request->id)->where('status', '>', Institution::INSTITUTION_SYS_STATUS_TWO)->first();
        $query->page_view = $query->page_view + 1;
        $query->save();

        $institytionType = InstitutionHomeType::getHomeTypeName($request->id);

        return [
            'id'                        => $query->id,
            'institution_name'          => $query->institution_name,
            'institution_address'       => $query->institution_address,
            'institution_img'           => $query->institution_img,
            'institution_detail'        => $query->institution_detail,
            'institution_tel'           => $query->institution_tel,
            'institution_type'          => $query->institution_type,
            'page_view'                 => $query->page_view,
            'status'                    => $query->status,
            'institytion_type'          => $institytionType,
            'created_at'                => date('Y-m-d H:i', strtotime($query->created_at))
        ];
    }



}

