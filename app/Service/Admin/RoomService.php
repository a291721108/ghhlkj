<?php

namespace App\Service\Admin;

use App\Models\Institution;
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

        $homeList = InstitutionHome::where('institution_id',$adminInfo->admin_institution_id)
            ->where('type',$request->typeId)
            ->where('instutution_status','>',InstitutionHome::Home_SYS_STATUS_THERE)->get();

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

        $institutionId = Institution::where('admin_id',$adminInfo->id)->first();
        $homeList = InstitutionHome::where('institution_id',$institutionId->id)->where('instutution_status','>',InstitutionHome::Home_SYS_STATUS_THERE)->select('institution_num')->get()->toarray();

        $homeArr = array_column($homeList,'institution_num',null);
        if (in_array($request->homeArr,$homeArr)) {

            return 'add_repetition';
        }

        $data = [
            'institution_id'        => $institutionId->id,
            'type'                  => $request->typeId,
            'institution_num'       => $request->homeArr,
            'instutution_status'    => InstitutionHome::Home_SYS_STATUS_ONE,
            'created_at'            => time(),
        ];

        InstitutionHome::insert($data);


        return 'success';
    }

    /**
     * 删除房间号
     */
    public static function delInstitutionHome($request)
    {

        $homeMsg = InstitutionHome::where('id',$request->homeId)->first();

        $homeMsg->instutution_status = InstitutionHome::Home_SYS_STATUS_THERE;
        $homeMsg->updated_at = time();

        if ($homeMsg->save()){
            return 'success';
        }
        return 'error';
    }

    /**
     * 编辑房间号
     */
    public static function upInstitutionHome($request)
    {

        $homeMsg = InstitutionHome::where('id',$request->homeId)->first();

        $homeMsg->institution_num = $request->institution_num;
        $homeMsg->updated_at = time();

        if ($homeMsg->save()){
            return 'success';
        }
        return 'error';
    }
}









