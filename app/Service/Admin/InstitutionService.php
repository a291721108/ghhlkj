<?php

namespace App\Service\Admin;

use App\Models\Institution;
use App\Models\InstitutionAdmin;

class InstitutionService
{

    /**
     * 机构添加
     */
    public static function addInstitution($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();

        $institutionArr = [
            'admin_id'              => $adminInfo->id,
            'institution_name'      => $request->institution_name,
            'institution_address'   => $request->institution_address,
            'institution_img'       => $request->institution_img,
            'institution_detail'    => $request->institution_detail,
            'institution_tel'       => $request->institution_tel,
            'institution_type'      => $request->institution_type,
            'status'                => Institution::INSTITUTION_SYS_STATUS_ONE,
            'created_at'            => time(),

        ];

        return Institution::insert($institutionArr);
    }

    /**
     * 机构编辑
     */
    public static function upInstitution($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();

        $institutionMsg = Institution::where('admin_id',$adminInfo->id)->first();
        $institutionMsg->institution_name       = $request->institution_name;
        $institutionMsg->institution_address    = $request->institution_address;
        $institutionMsg->institution_img        = $request->institution_img;
        $institutionMsg->institution_detail     = $request->institution_detail;
        $institutionMsg->institution_tel        = $request->institution_tel;
        $institutionMsg->institution_type       = $request->institution_type;
        $institutionMsg->updated_at             = time();

        if ($institutionMsg->save()){
            return 'success';
        }
        return 'error';
    }

    /**
     * 机构查看
     */
    public static function getInstitution($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();

        $institutionMsg = Institution::where('admin_id', $adminInfo->id)
            ->where('admin_id', $adminInfo->id)
            ->where('status', '>', Institution::INSTITUTION_SYS_STATUS_TWO)
            ->first();

        if (empty($institutionMsg)){
            return [];
        }
        $imgArr = explode(",", $institutionMsg->institution_img);

        return [
            'institution_name'      => $institutionMsg->institution_name,
            'institution_address'   => $institutionMsg->institution_address,
            'institution_img'       => $imgArr,
            'institution_detail'    => $institutionMsg->institution_detail,
            'institution_tel'       => $institutionMsg->institution_tel,
            'institution_type'      => Institution::INS_TYPE_ARRAY[$institutionMsg->institution_type],
            'page_view'             => $institutionMsg->page_view,
            'status'                => Institution::INS_MSG_ARRAY[$institutionMsg->status],
            'created_at'            => $institutionMsg->created_at,
        ];
    }
}









