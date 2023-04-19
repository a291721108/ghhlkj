<?php

namespace App\Service\Front;

use App\Models\Booking;
use App\Models\Friend;
use App\Models\Institution;
use App\Models\InstitutionHome;
use App\Models\InstitutionHomeFacilities;
use App\Models\InstitutionHomeType;
use App\Models\Kinship;
use App\Models\Order;
use App\Models\User;
use App\Service\Common\FunService;
use Illuminate\Support\Facades\Auth;

class FriendService
{
    /**
     * 获取亲友状态
     * @param $request
     * @return mixed
     */
    public static function getRelativeStatus()
    {
        $query = Kinship::where('kinship_type','>',Kinship::KINSHIP_TYPE_TWO)->get();

        $data = [];
        foreach ($query as $k => $v) {
            // 处理回参
            $data[$k] = [
                'id'                    => $v['id'],
                'kinship_name'          => $v['kinship_name'],
                'kinship_type'          => Kinship::KINSHIP_TYPE_MSG_ARRAY[$v['kinship_type']]
            ];
        }
        return $data;
    }

    /**
     * 添加亲友
     * @param $request
     * @return mixed
     */
    public static function relativeStatusAdd($request)
    {
        $userInfo = Auth::user();

        $data = [
            'user_id'           => $userInfo->id,
            'friend_name'       => $request->friend_name,
            'friend_card'       => $request->friend_card,
            'friend_kinship'    => $request->friend_kinship,
            'friend_tel'        => $request->friend_tel,
            'created_at'        => time(),
        ];

        return Friend::insert($data);
    }

    /**
     * 亲友列表
     * @param $request
     * @return mixed
     */
    public static function relativeStatusList()
    {
        $userInfo = Auth::user();

        $result = Friend::where('friend_status','>',Friend::FRIEND_STATUS_TWO)->where('user_id',$userInfo->id)->get()->toArray();

        foreach ($result as &$v) {
            // 处理回参
            $v['user_id']               = User::getUserInfoById($v['user_id']);
            $v['friend_status']         = Friend::FRIEND_STATUS_MSG_ARRAY[$v['friend_status']];
            $v['friend_kinship']        = Kinship::getIdByname($v['friend_kinship']);
            $v['created_at']            = hourMinuteSecond(strtotime($v['created_at']));
        }
        return $result;

    }
}


