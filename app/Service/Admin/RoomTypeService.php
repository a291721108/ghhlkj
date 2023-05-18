<?php

namespace App\Service\Admin;

use App\Models\Institution;
use App\Models\InstitutionAdmin;
use App\Models\InstitutionHome;
use App\Models\InstitutionHomeType;
use App\Models\UserSend;
use App\Service\Common\RedisService;
use Illuminate\Support\Facades\Auth;

class RoomTypeService
{
    /**
     * 添加房间类型
     */
    public static function addHomeType($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();
        $institutionId = Institution::where('admin_id',$adminInfo->id)->value('id');

        $homeTypeImg = $request->homeTypeImg;
        $img = implode(",",$homeTypeImg);

        $homeNum = $request->homeNum;

        $homeTypeArr = [
            'institution_id'    => $institutionId,
            'home_type'         => $request->home_type,
            'home_price'        => $request->home_price,
            'home_size'         => $request->home_size,
            'home_facility'     => $request->home_facility,
            'home_detail'       => $request->home_detail,
            'home_img'          => $img,
            'status'            => InstitutionHomeType::Home_TYPE_SYS_STATUS_ONE,
            'created_at'        => time()
        ];

        $homeTypeId = InstitutionHomeType::insertGetId($homeTypeArr);

        foreach ($homeNum as &$v){
            $data = [
                'institution_id'        => $institutionId,
                'type'                  => $homeTypeId,
                'institution_num'       => $v,
                'instutution_status'    => InstitutionHome::Home_SYS_STATUS_ONE,
                'created_at'            => time(),
            ];

            InstitutionHome::insert($data);
        }

        return "success";
    }


}









