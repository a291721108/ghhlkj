<?php

namespace App\Service\Front;

use App\Models\Booking;
use App\Models\Institution;
use App\Models\InstitutionHome;
use App\Models\InstitutionHomeFacilities;
use App\Models\InstitutionHomeType;
use App\Models\Order;
use App\Models\User;
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
            'user_id'           => $userInfo->id,
            'institution_id'    => $request->institution_id,
            'home_type_id'      => $request->home_type_id,
            'check_in_date'     => $request->check_in_date,
            'contacts'          => $request->contacts,
            'contact_way'       => $request->contact_way,
            'remark'            => $request->remark ?? '',
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

        $query    = Booking::where('status', '>', Booking::BOOKING_SYS_TYPE_FOUR)->where('user_id',$userId)->get()->toArray();

        foreach ($query as $k => $v) {
            // 处理回参
            $data[$k] = [
                'user_id'           => $userInfo->id,
                'user_name'         => $userInfo->name,
                'institution_id'    => Institution::getInstitutionId($v['institution_id']),
                'home_type_id'      => InstitutionHomeType::getInstitutionIdByName($v['home_type_id']),
                'check_in_date'     => ytdTampTime($v['check_in_date']),
                'contacts'          => $v['contacts'],
                'contact_way'       => $v['contact_way'],
                'remark'            => $v['remark'] ?? '',
                'status'            => Booking::INS_MSG_ARRAY[$v['status']],
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

        $bookingMsg = Booking::where('id',$bookingId)->first();

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

