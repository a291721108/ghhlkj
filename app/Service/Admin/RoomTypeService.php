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

    /**
     * 获取房间类型
     */
    public static function getHomeType($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();
        $institutionId = Institution::where('admin_id',$adminInfo->id)->value('id');
        $homeType = InstitutionHomeType::where('institution_id',$institutionId)->get()->toArray();

        $data = [];
        foreach ($homeType as $k => $v){
            $data[] = [
                'id'                => $v['id'],
                'institution_id'    => $v['institution_id'],
                'home_type'         => $v['home_type'],
                'home_img'          => explode(",",$v['home_img'])[0] ?? '',
                'home_price'        => $v['home_price'],
                'home_size'         => $v['home_size'],
                'home_facility'     => $v['home_facility'],
                'home_detail'       => $v['home_detail'],
                'status'            => $v['status'],
                'created_at'        => $v['created_at'],
            ];
        }
        return $data;
    }

    /**
     * 根据id获取房间类型
     */
    public static function homeTypeInfo($request)
    {

        $homeType = InstitutionHomeType::where('id',$request->homeTypeId)->first();
        $homeNum = InstitutionHome::where('type',$homeType->id)->get()->toArray();

        $home = [];
        foreach ($homeNum as $k => $v){
            $home[] = [
                'id'   => $v['id'],
                'institution_num'   => $v['institution_num'],
                'instutution_status'   => $v['instutution_status'],
            ];
        }

        return [
            'id'                => $homeType->id,
            'institution_id'    => $homeType->institution_id,
            'home_type'         => $homeType->home_type,
            'home_img'          => explode(",",$homeType->home_img),
            'home_price'        => $homeType->home_price,
            'home_size'         => $homeType->home_size,
            'home_facility'     => $homeType->home_facility,
            'home_detail'       => $homeType->home_detail,
            'status'            => $homeType->status,
            'home_num'          => $home,
        ];
    }

    /**
     * 修改房间类型
     */
    public static function upHomeType($request)
    {

        $homeType = InstitutionHomeType::where('id',$request->homeTypeId)->first();

        $homeTypeImg = $request->homeTypeImg;
        $img = implode(",",$homeTypeImg);
        $homeNum = $request->homeNum;

        $homeTypeArr = [
            'institution_id'    => $homeType->institution_id,
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
                'institution_id'        => $homeType->institution_id,
                'type'                  => $homeTypeId,
                'institution_num'       => $v,
                'instutution_status'    => InstitutionHome::Home_SYS_STATUS_ONE,
                'created_at'            => time(),
            ];

            InstitutionHome->save($data);
        }

        return "success";
    }
}









