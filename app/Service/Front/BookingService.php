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


}

