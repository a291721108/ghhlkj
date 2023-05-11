<?php

namespace App\Service\Front;

use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Institution;
use App\Models\InstitutionHomeType;
use App\Models\Order;
use App\Models\User;
use App\Models\UserExt;
use App\Service\Common\FunService;

class BookingService
{
    /**
     * 预约订单
     */
    public static function agencyAppointment($request)
    {
        $userInfo = User::getUserInfo();

        $data = [
            'userId'            => $userInfo->id,                               //用户id
            'orderName'         => UserExt::getMsgByUserName($userInfo->id),    //联系人
            'orderPhone'        => $request->orderPhone,                        //联系方式
            'orderIDcard'       => UserExt::getMsgByUserCard($userInfo->id),    //身份证
            'institutionId'     => $request->institutionId,                     //机构id
            'typeId'            => $request->typeId,                            //房型id
            'arrireDate'        => strtotime($request->arrireDate),             //看房时间
            'orderState'        => Booking::BOOKING_SYS_TYPE_ONE,               //状态
            'roomId'            => FunService::orderNumber(),                   //订单编号
            'remark'            => $request->remark,                            //备注
            'created_at'        => time()
        ];

        return Booking::insert($data);
    }

    /**
     * @param $request
     * @return array
     */
    public static function reservationList($request)
    {
        $userInfo = User::getUserInfo();
        $userId = $userInfo->id;

        $query    = Order::where('userId',$userId)->get()->toArray();

        foreach ($query as $k => $v) {
            // 处理回参
            $data[$k] = [
                'id'                => $v['id'],
                'userId'           => $userInfo->id,
                'orderName'         => $userInfo->name,
                'orderPhone'        => $v['orderPhone'],
                'orderIDcard'       => $v['orderIDcard'],
                'institutionId'     => Institution::getInstitutionId($v['institutionId']),
                'typeId'            => InstitutionHomeType::getInstitutionIdByName($v['typeId']),
                'arrireDate'        => ytdTampTime($v['arrireDate']),
                'orderState'        => Booking::INS_MSG_ARRAY[$v['orderState']],
                'roomId'            => $v['roomId'],
                'remark'            => $v['remark'],
                'created_at'        => hourMinuteSecond(time())
            ];
        }

        return $data;
    }

    /**
     * 预约订单
     */
    public static function getBookOneMsg($request)
    {
        $userInfo = User::getUserInfo();
        $bookingId = $request->bookingId;

        $bookingMsg = Booking::where('id',$bookingId)->first();

        // 返回用户信息
        return [
            "id"                    => $bookingMsg->id,
            'orderName'             => UserExt::getMsgByUserName($userInfo->id),
            'orderIDcard'           => UserExt::getMsgByUserCard($userInfo->id),
            'orderPhone'            => $bookingMsg->orderPhone,
            'institution_name'      => Institution::getInstitutionId($bookingMsg->institutionId),
            'typeId'                => InstitutionHomeType::getInstitutionIdByName($bookingMsg->typeId),
            'arrireDate'            => hourMinuteSecond($bookingMsg->arrireDate),
            'remark'                => $bookingMsg->remark,
            'roomId'                => $bookingMsg->roomId,
            'orderState'            => Booking::INS_MSG_ARRAY[$bookingMsg->orderState],
            'created_at'            => hourMinuteSecond(strtotime($bookingMsg->created_at)),
        ];

    }

}

