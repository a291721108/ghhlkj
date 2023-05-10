<?php

namespace App\Service\Front;

use App\Models\BookingRoom;
use App\Models\Institution;
use App\Models\InstitutionHomeType;
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

    /**
     * 获取单条订房信息
     */
    public static function getBookRoomOneMsg($request)
    {
        $userInfo = User::getUserInfo();
        $bookingId = $request->bookingRoomId;

        $bookingRoomMsg = BookingRoom::where('id',$bookingId)->first();

        // 返回用户信息
        return [
            "id"                => $bookingRoomMsg->id,
            'orderName'         => UserExt::getMsgByUserName($userInfo->id),
            'orderIDcard'       => UserExt::getMsgByUserCard($userInfo->id),
            'orderPhone'        => $bookingRoomMsg->orderPhone,
            'institution_name'  => Institution::getInstitutionId($bookingRoomMsg->institutionId),
            'typeId'            => InstitutionHomeType::getInstitutionIdByName($bookingRoomMsg->typeId),
            'startDate'         => hourMinuteSecond($bookingRoomMsg->startDate),
            'leaveDate'         => hourMinuteSecond($bookingRoomMsg->startDate),
            'payment'           => $bookingRoomMsg->payment,
            'remark'            => $bookingRoomMsg->remark,
            'roomId'            => $bookingRoomMsg->roomId,
            'status'            => BookingRoom::INS_MSG_ARRAY[$bookingRoomMsg->orderState],
            'created_at'        => hourMinuteSecond(strtotime($bookingRoomMsg->created_at)),
        ];

    }
}

