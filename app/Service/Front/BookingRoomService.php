<?php

namespace App\Service\Front;

use App\Models\BookingRoom;
use App\Models\User;
use App\Models\UserExt;
use App\Service\Common\FunService;

class BookingRoomService
{
    /**
     * 订房信息
     */
    public static function reservationInformation($request)
    {
        $userInfo = User::getUserInfo();

        // todo 待完善  定价支付  500

        $data = [
            'userId'         => $userInfo->id,
            'orderName'      => UserExt::getMsgByUserName($userInfo->id),
            'orderPhone'     => $request->orderPhone,
            'orderIDcard'    => UserExt::getMsgByUserCard($userInfo->id),
            'institutionId'  => $request->institutionId,
            'typeId'         => $request->typeId,
            'startDate'      => strtotime($request->startDate),
            'leaveDate'      => strtotime($request->leaveDate),
            'payment'        => $request->payment,
            'status'         => BookingRoom::ROOM_SYS_TYPE_ZERO,
            'roomId'         => FunService::orderNumber(),
            'remark'         => $request->remark,
            'created_at'     => time()
        ];

        return BookingRoom::insert($data);
    }
}

