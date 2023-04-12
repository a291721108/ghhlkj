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
        $data = InstitutionHomeType::where('status', '>', InstitutionHomeType::Home_TYPE_SYS_STATUS_TWO)
            ->get()
            ->toArray();
        return $data;
    }

    /**
     * 房间类型详情页
     */
    public static function organizationTypeDetails($request)
    {
        $organizationId = $request->id;

        $result = InstitutionHomeType::where('status', '>', InstitutionHomeType::Home_TYPE_SYS_STATUS_TWO)
            ->where('id', $organizationId)
            ->get()->toaRRAY();

        foreach ($result as $k => $v) {
            // 处理回参
            $data[$k] = [
                'id' => $v['id'],
                'institution_id'    => Institution::getInstitutionId($v['institution_id']),
                'home_type'         => $v['home_type'],
                'home_img'          => $v['home_img'],
                'home_price'        => $v['home_price'],
                'home_detail'       => $v['home_detail'],
                'home_facility'     => $v['home_facility'],
                'home_size'         => $v['home_size'],
                'status'            => InstitutionHomeType::Home_MSG_ARRAY[$v['status']],
                'created_at'        => strtotime($v['created_at'])
            ];
        }

        return $data;


//        $result = Institution::with('products')
//            ->where('id',$tissue_id)
//            ->get()->toArray();


    }

}

