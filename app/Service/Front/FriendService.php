<?php

namespace App\Service\Front;

use App\Models\Booking;
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
     * 添加亲友
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
}


