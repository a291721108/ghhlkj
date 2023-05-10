<?php

namespace App\Service\Admin;

use App\Models\Booking;
use App\Models\Institution;
use App\Models\InstitutionAdmin;

class OrderNotificationService
{

    /**
     * 同意预约
     */
    public static function subscribeCheck($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();
        $bookingId = $request->bookingId;

        $institution = Booking::where('institutionId',$adminInfo->admin_institution_id)
            ->where('orderState',Booking::BOOKING_SYS_TYPE_ONE)
            ->get()->toArray();

        dd($institution);die();

        $bookingMsg = Booking::where('id',$bookingId)->first();

        var_dump($adminInfo);die();
        return "123";
    }

}
