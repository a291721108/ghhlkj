<?php

namespace App\Service\Front;

use App\Models\Institution;

class OrganizationService
{
    public static function organizationList($request): array
    {
        $page     = $request->page ?? 1;
        $pageSize = $request->page_size ?? 20;

        // 查询列表
        $query = Institution::where('status', '>', Institution::INSTITUTION_SYS_STATUS_TWO)
            ->orderBy('id','desc');

        $result = Institution::getProListPage($query, $page, $pageSize);
        $result['data'] = self::dealReturnData($result['data']);

        return  $result;
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
     * 获取机构浏览量前五
     */
    public static function tissueCount()
    {

        // 查询列表
        $query = Institution::where('status', '>', Institution::INSTITUTION_SYS_STATUS_TWO)
            ->orderBy('page_view','desc')
            ->limit(3)
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

}

