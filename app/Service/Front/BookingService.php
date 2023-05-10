<?php

namespace App\Service\Front;

use App\Models\Booking;
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
            'userId'         => $userInfo->id,
            'orderName'      => UserExt::getMsgByUserName($userInfo->id),
            'orderPhone'     => $request->orderPhone,
            'orderIDcard'    => UserExt::getMsgByUserCard($userInfo->id),
            'institutionId'  => $request->institutionId,
            'typeId'         => $request->typeId,
            'arrireDate'     => strtotime($request->arrireDate),
            'orderState'     => Booking::BOOKING_SYS_TYPE_ONE,
            'roomId'         => FunService::orderNumber(),
            'remark'         => $request->remark,
            'created_at'     => time()
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

        $query    = Booking::where('userId',$userId)->get()->toArray();

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
    public static function userReservationRecord($request)
    {
        $userInfo = User::getUserInfo();
        $bookingId = $request->booking_id;

        $bookingMsg = Order::where('id',$bookingId)->first();

        // 返回用户信息
        return [
            "id"                    => $bookingMsg->id,
            'user_name'             => $userInfo->name,
            'institution_name'      => Institution::getInstitutionId($bookingMsg->institution_id),
            'home_type_name'        => InstitutionHomeType::getInstitutionIdByName($bookingMsg->home_type_id),
            'check_in_date'         => hourMinuteSecond($bookingMsg->check_in_date),
            'contacts'              => $bookingMsg->contacts,
            'contact_way'           => $bookingMsg->contact_way,
            'remark'                => $bookingMsg->remark,
            'status'                => Booking::INS_MSG_ARRAY[$bookingMsg->status],
            'created_at'            => hourMinuteSecond(strtotime($bookingMsg->created_at)),
        ];

    }
}

