<?php

namespace App\Service\Common;

use App\Models\Institution;
use App\Models\InstitutionHome;
use App\Models\InstitutionHomeFacilities;
use App\Models\InstitutionHomeType;
use App\Models\Order;
use App\Models\User;
use App\Models\UserExt;
use Illuminate\Support\Facades\Crypt;

class FunService
{
    /**
     * 机构数据
     * @return array
     */
    public static function getInstitutionData(): array
    {
        $homeData = Institution::where('status', '>', InstitutionHome::Home_SYS_STATUS_TWO)->select('id', 'institution_name', 'institution_address', 'institution_img', 'institution_detail')->get()->toArray();
        return array_column($homeData, null, 'id');
    }

    /**
     * 房间数据
     * @return array
     */
    public static function getHomeData(): array
    {
        $homeData = InstitutionHome::where('status', '>', InstitutionHome::Home_SYS_STATUS_TWO)->select('id', 'institution_id', 'home_type', 'home_img', 'home_pic')->get()->toArray();
        return array_column($homeData, null, 'id');
    }

    /**
     * 获取所有房间类型
     */
    public static function getHomeType(): array
    {
        $homeType = InstitutionHomeType::where('status', '>', InstitutionHomeType::Home_TYPE_SYS_STATUS_TWO)
            ->select('id', 'home_type_name', 'status')
            ->get()
            ->toArray();

        return array_column($homeType, null, 'id');
    }

    /**
     * 房间设备
     * @return array
     */
    public static function getHomeFaclitiy(): array
    {
        $HomeFaclitiy = InstitutionHomeFacilities::where('id','>', 0)
            ->select('id', 'hotel_facilities')
            ->get()
            ->toArray();

        return  array_column($HomeFaclitiy, null, 'id');
    }

    /**
     * 订单编号
     * @return
     * 公司简称（光晖互联=gh）+创建时间（20220727）+当年截至目前排序的序号（00001）
     */
    public static function orderNumber()
    {

        $orderCount = Order::count();

        $num=str_pad($orderCount + 1 ,5,"0",STR_PAD_LEFT);

        return 'GH' . date("YmdHi",time()) . $num;
    }

    /**
     * 用户编号
     * @return
     * 公司简称（光晖互联=gh）+创建时间（20220727）+当年截至目前排序的序号（00001）
     */
    public static function userNumber()
    {

        $userCount = User::count();

        $num=str_pad($userCount + 1 ,4,"0",STR_PAD_LEFT);

        return '游客' .  $num;
    }
}
