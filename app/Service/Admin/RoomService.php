<?php

namespace App\Service\Admin;

use App\Models\InstitutionAdmin;
use App\Models\InstitutionHome;
use App\Models\UserSend;
use App\Service\Common\RedisService;
use Illuminate\Support\Facades\Auth;

class RoomService
{

    /**
     * 获取机构房间
     */
    public static function getInstitutionHomeList($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();

        $homeList = InstitutionHome::where('institution_id',$adminInfo->admin_institution_id)->where('type',$request->typeId)->get();

        $data = [];

        foreach ($homeList as $k => $v) {

            // 处理回参
            $data[$k] = [
                'id'                    => $v->id,
                'institution_num'       => $v->institution_num,
                'instutution_status'    => InstitutionHome::Home_MSG_ARRAY[$v->instutution_status],
                'created_at'            => hourMinuteSecond(strtotime($v->created_at)),
            ];
        }

        return $data;
    }

    /**
     * 添加机构房间
     */
    public static function addInstitutionHome($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();

        $homeList = InstitutionHome::where('institution_id',$adminInfo->admin_institution_id)->whereIn('institution_num', $request->homeArr)->pluck('institution_num')->toArray();
        if (!empty($homeList)) {

            return 'add_repetition';
        }

        $uniqueData = array_diff($request->homeArr, $homeList);

        foreach ($uniqueData as $v) {

            $data = [
                'institution_id'        => $adminInfo->admin_institution_id,
                'type'                  => $request->typeId,
                'institution_num'       => $v,
                'instutution_status'    => InstitutionHome::Home_SYS_STATUS_ONE,
                'created_at'            => time(),
            ];

            InstitutionHome::insert($data);
        }

        return 'success';
    }
}









