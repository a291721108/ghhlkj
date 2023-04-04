<?php

namespace App\Service\Front;

use App\Models\Institution;
use App\Models\InstitutionHome;
use App\Models\InstitutionHomeFacilities;
use App\Models\InstitutionHomeType;
use App\Service\Common\FunService;

class HomeTypeService
{

    /**
     * 获取所有房间类型
     */
    public static function homeTypeList()
    {
        $data = InstitutionHomeType::where('status','>',InstitutionHomeType::Home_TYPE_SYS_STATUS_TWO)
            ->get()
            ->toArray();
        return $data;
    }

    /**
     * 房间类型详情页
     */
    public static function tissueDetailPage($request){
        $tissue_id = $request->tissue_id;

        $getHomeType = FunService::getHomeType();

        $result = Institution::with('products')
            ->where('id',$tissue_id)
            ->get()->toArray();

        $homeFaclitiy = FunService::getHomeFaclitiy();

        foreach ($result[0]['products'] as $k => $v) {

            $joinFacility = explode(',', $v['home_facility']);

            foreach ($joinFacility as &$kv) {
                $kv = $homeFaclitiy[$kv] ?? '';
                // 处理回参
                $data[$k] = [
                    'id'                        => $v['id'],
                    'institution_id'            => $v['institution_id'],
                    'home_type'                 => InstitutionHomeType::getHomeTypeName($v['home_type']),
                    'home_img'                  => $v['home_img'],
                    'home_pic'                  => $v['home_pic'],
                    'home_size'                 => $v['home_size'],
                    'home_detal'                => $v['home_detal'],
                    'home_facility'             => $joinFacility,
                    'status'                    => $v['status'],
                    'created_at'                => timestampTime(strtotime($v['created_at']))
                ];
            }
        }

        return $data;

    }

}

