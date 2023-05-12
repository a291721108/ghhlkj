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
            'amount_paid'       => $request->amount_paid,
            'payment_method'    => $request->payment_method,
            'institution_id'    => $request->institution_id,
            'institution_type'  => $request->institution_type,
            'start_date'        => strtotime($request->start_date),
            'end_date'          => strtotime($request->end_date),
            'order_phone'       => $request->order_phone,
            'order_remark'      => $request->order_remark,
            'contacts'          => $request->contacts,
            'contacts_card'     => $request->contacts_card,
            'status'            => Order::ORDER_SYS_TYPE_FOUR,
            'created_at'        => time(),
        ];

        return Order::insert($data);
    }



    /**
     * 订单列表
     * @param $request
     * @return array
     */
    public static function orderList($request): array
    {
        $userInfo = Auth::user();
        $page     = $request->page ?? 1;
        $pageSize = $request->page_size ?? 20;

        $userId = $userInfo->id;

        $query    = self::makeSearchWhere($request,$userId);

        // 获取分页数据
        $result = (new Order())->getMsgPageList($query, $page, $pageSize);

        // 处理特殊字段
        $result['data'] = self::dealReturnData($result['data']);

        return $result;
    }

    /**
     * @param $request
     * @return mixed
     */
    protected static function makeSearchWhere($request,$userId)
    {
        $query = Order::where('user_id', $userId);

        // 状态查询
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }

        return $query;
    }

    /**
     * 处理数组
     * @param $query
     * @param array $data
     * @return array
     */
    public static function dealReturnData($query, array $data = [])
    {

        foreach ($query as $k => $v) {

            // 处理回参
            $data[$k] = [
                'id'                 => $v['id'],
                'institution_id'     => Institution::getInstitutionId($v['institution_id']),
                'institution_type'   => InstitutionHomeType::getInstitutionIdByName($v['institution_type']),
                'total_amount'       => $v['total_amount'],
                'amount_paid'        => $v['amount_paid'],
                'contacts'           => $v['contacts'],
                'visitDate'          => ytdTampTime($v['visitDate']),
                'search_time'     => [
                    ytdTampTime($v['start_date']),
                    ytdTampTime($v['end_date'])
                ],
                'status'             => Order::INS_MSG_ARRAY[$v['status']],

            ];
        }

        return $data;
    }
//----------------------------------------------------------------------------
    /**
     * 订单详情
     */
    public static function userReservationRecord($request)
    {
        $userInfo = User::getUserInfo();
        $orderId = $request->orderId;

        $orderMsg = Order::where('id',$orderId)->first();

        // 返回用户信息
        return [
            "id"                    => $orderMsg->id,
            'user_id'               => $userInfo->name,
            'order_no'              => $orderMsg->order_no,
            'total_amount'          => $orderMsg->total_amount,
            'amount_paid'           => $orderMsg->amount_paid,
            'payment_method'        => $orderMsg->amount_paid ? '支付宝' : '微信',
            'institution_id'        => Institution::getInstitutionName($orderMsg->institution_id),
            'institution_type'      => InstitutionHomeType::getInstitutionIdByName($orderMsg->institution_type),
            'discount_coupon'       => $orderMsg->discount_coupon,
            'visitDate'             => ytdTampTime($orderMsg->visitDate),
            'start_date'            => ytdTampTime($orderMsg->start_date),
            'end_date'              => ytdTampTime($orderMsg->end_date),
            'roomNum'               => $orderMsg->roomNum,
            'order_phone'           => $orderMsg->order_phone,
            'order_remark'          => $orderMsg->order_remark,
            'contacts'              => $orderMsg->contacts,
            'contacts_card'         => $orderMsg->contacts_card,
            'status'                => Order::INS_MSG_ARRAY[$orderMsg->status],
            'created_at'            => hourMinuteSecond(strtotime($orderMsg->created_at)),
            ];

    }

    /**
     * 订单删除
     */
    public static function cancelReservation($request)
    {
        $useInfo = User::getUserInfo();
        $orderId = $request->orderId;

        $orderMsg = Order::where('user_id',$useInfo->id)->where('id',$orderId)->first();
        $orderMsg->status = Order::ORDER_SYS_TYPE_ZERO;

        if ($orderMsg->save()){
            return 'success';
        }

        return 'error';
    }

    /**
     * 订单支付
     */
    public static function paymentOrder($request)
    {

        $useInfo = User::getUserInfo();
        $orderId = $request->orderId;

        $orderMsg = Order::where('user_id',$useInfo->id)->where('id',$orderId)->where('status',Order::ORDER_SYS_TYPE_ONE)->first();
        $orderMsg->total_amount = $request->total_amount;
        $orderMsg->amount_paid  = $request->amount_paid;
        $orderMsg->wait_pay     = $request->wait_pay;
        $orderMsg->payment_method = '1';
        $orderMsg->start_date   = $request->start_date;
        $orderMsg->end_date     = $request->end_date;
        $orderMsg->order_remark = $request->order_remark;
        $orderMsg->status       = Order::ORDER_SYS_TYPE_TWO;

        // todo 支付全部金额成功  否则失败   存入数据库
        if ($orderMsg->save()){
            return 'success';
        }

        return 'error';
    }
}


