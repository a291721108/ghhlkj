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
use Illuminate\Support\Facades\Auth;

class OrderService
{
    /**
     * 下单
     */
    public static function placeAnOrder($request)
    {
        $userInfo = User::getUserInfo();

        $data = [
            'user_id'           =>$userInfo->id,
            'order_no'          => FunService::orderNumber(),
            'total_amount'      => $request->total_amount,
            'payment_method'    => $request->payment_method,
            'institution_id'    => $request->institution_id,
            'institution_type'  => $request->institution_type,
            'start_date'        => strtotime($request->start_date),
            'end_date'          => strtotime($request->end_date),
            'order_phone'       => $request->order_phone,
            'order_remark'      => $request->order_remark,
            'contacts'          => $request->contacts,
            'contacts_card'     => $request->contacts_card,
            'status'            => Order::ORDER_SYS_TYPE_ONE(),
            'created_at'        => time(),
        ];

        return Order::insert($data);
    }



    /**
     * 订单列表
     * @param $request
     * @return array
     */
    public static function orderList($request)
    {
        $userInfo = Auth::user();

        $page     = $request->page ?? 1;
        $pageSize = $request->page_size ?? 20;
        $status   = $request->status;

        $where = [
            'user_id' => $userInfo->id,
        ];

        // 按状态搜索
        if (isset($status)) {
            $where['status'] = $status;
        }

        // 获取分页数据
        $result = (new Order())->getMsgPageList($page, $pageSize,['*'], $where);
        foreach ($result['data'] as &$v) {
            // 处理回参
            $v['user_id']               = User::getUserInfoById($v['user_id']);
            $v['status']                = Order::INS_MSG_ARRAY[$v['status']];
            $v['institution_id']        = Institution::getInstitutionId($v['institution_id']);
            $v['institution_type']      = InstitutionHomeType::getInstitutionIdByName($v['institution_type']);
            $v['start_date']            = hourMinuteSecond($v['start_date']);
            $v['end_date']              = hourMinuteSecond($v['end_date']);
            $v['created_at']            = hourMinuteSecond(strtotime($v['created_at']));
        }
        return $result;
    }


}


