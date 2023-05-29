<?php

namespace App\Service\Admin;

use App\Models\Institution;
use App\Models\InstitutionAdmin;
use App\Models\Order;
use App\Models\User;
use App\Models\UserExt;

class ClientService
{

    /**
     * 获取顾客列表
     */
    public static function getClientList($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();

        $institutionData = Institution::where('admin_id',$adminInfo->id)->first();
        $clientData = Order::where('institution_id',$institutionData->id)->select('id','user_id','amount_paid','status')->get()->toArray();

        $userData = array_column($clientData, null, 'user_id');
        $uniqueUserIds = [];
        foreach ($userData as $element) {
            if ($element['amount_paid']){
                $deal = '是';
            }else{
                $deal = '否';
            }

            $uniqueUserIds[] = [
                'user'  => UserExt::getMsgByUserName($element['user_id']),
                'info'  => User::getUserInfoById($element['user_id']),
                'card'  => UserExt::getMsgByUserCard($element['user_id']),
                'deal'  => $deal
            ];
        }

        return $uniqueUserIds;
    }


}









