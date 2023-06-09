<?php

namespace App\Service\Front;

use App\Models\Booking;
use App\Models\Institution;
use App\Models\InstitutionHome;
use App\Models\InstitutionHomeFacilities;
use App\Models\InstitutionHomeType;
use App\Models\Order;
use App\Models\OrderRefunds;
use App\Models\OrderRenewal;
use App\Models\User;
use App\Service\Common\FunService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

            if ($v['refundNot'] == Order::ORDER_CHECK_OUT_ONE && $v['status'] == Order::ORDER_SYS_TYPE_THERE){
                $reFound = OrderRefunds::where('order_id',$v['id'])->first();
                $refund_amount = $reFound['amount'];
            }

            // 处理回参
            $data[$k] = [
                'id'                 => $v['id'],
                'institution_id'     => Institution::getInstitutionId($v['institution_id']),
                'institution_type'   => InstitutionHomeType::getInstitutionIdByName($v['institution_type']),
                'total_amount'       => $v['total_amount'],
                'amount_paid'        => $v['amount_paid'],
                'roomNum'            => InstitutionHome::getHomeIdByName($v['roomNum']),
                'contacts'           => $v['contacts'],
                'visitDate'          => ytdTampTime($v['visitDate']),
                'search_time'     => [
                    ytdTampTime($v['start_date']),
                    ytdTampTime($v['end_date'])
                ],
                'refundNot'          => $v['refundNot'],
                'refund_amount'      => $refund_amount ?? '',
                'renewalNot'         => $v['renewalNot'],
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

        $created_at = $orderMsg->created_at;
        $start_date = $orderMsg->start_date;
        $end_date   = $orderMsg->end_date;
        $remarkData = $orderMsg->order_remark;
        $refundTime = '';

        if ($orderMsg->status == Order::ORDER_SYS_TYPE_TWO) {
            if ($orderMsg->renewalNot == Order::ORDER_RENEW_TWO) {
                $remark = OrderRenewal::where('order_id', $orderId)->first();
                $created_at = $remark->created_at;
            }

            if ($orderMsg->refundNot == Order::ORDER_RENEW_TWO) {
                $refund = OrderRefunds::where('order_id', $orderId)->where('status',OrderRefunds::ORDER_CHECK_OUT_ZERO)->first();
                $refundTime = $refund->created_at;
            }
        }

//        elseif ($orderMsg->status == Order::ORDER_RENEW_TWO && $orderMsg->renewalNot != Order::ORDER_RENEW_ZERO) {
//            $remark = OrderRenewal::where('order_id', $orderId)->first();
//            $remarkData = $remark->remark;
//        }
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
            'start_date'            => ytdTampTime($start_date),
            'end_date'              => ytdTampTime($end_date),
            'roomNum'               => InstitutionHome::getHomeIdByName($orderMsg->roomNum),
            'order_phone'           => $orderMsg->order_phone,
            'order_remark'          => $remarkData,
//            'remark'                => $remark->remark,
            'contacts'              => $orderMsg->contacts,
            'contacts_card'         => $orderMsg->contacts_card,
            'status'                => Order::INS_MSG_ARRAY[$orderMsg->status],
            'created_at'            => hourMinuteSecond($created_at),
            'refundTime'            => hourMinuteSecond($refundTime) ?? '',
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
        $orderMsg->updated_at = time();

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

        // todo 支付全部金额成功  否则失败   存入数据库


        if ($orderMsg->wait_pay == 0 && $orderMsg->total_amount == $orderMsg->amount_paid){
            $orderMsg->status       = Order::ORDER_SYS_TYPE_TWO;
            return $orderMsg->save();
        }

        $orderMsg->total_amount = $request->total_amount;
        $orderMsg->amount_paid  = $request->amount_paid;
        $orderMsg->wait_pay     = $request->wait_pay;
        $orderMsg->payment_method = '1';
        $orderMsg->start_date   = strtotime($request->start_date);
        $orderMsg->end_date     = strtotime($request->end_date);
        $orderMsg->contacts     = $request->contacts;
        $orderMsg->order_remark = $request->order_remark;
        $orderMsg->status       = Order::ORDER_SYS_TYPE_TWO;
        $orderMsg->created_at   = time();

        if ($orderMsg->save()){
            return 'success';
        }

        return 'error';
    }

    /**
     * 申请退房
     */
    public static function checkOutApply($request)
    {

        $userInfo = User::getUserInfo();
        $orderId = $request->order_id;

        $refundDataExists  = OrderRefunds::where('order_id', $orderId)->where('status', OrderRefunds::ORDER_CHECK_OUT_ZERO)->exists();
        if ($refundDataExists){
            return 'application_submitted';
        }

        $orderData = Order::find($orderId);
        $orderData->refundNot   = Order::ORDER_CHECK_OUT_TWO;
        $orderData->updated_at  = time();
        $orderData->save();

        $orderCheckArr = [
            'order_id'          => $orderId,
            'guest_name'        => $userInfo->id,
            'created_at'        => time(),

        ];

        if (OrderRefunds::insert($orderCheckArr)){
            return 'success';
        }

        return 'error';
    }

    /**
     * 取消申请退房
     */
    public static function offCheckOutApply($request)
    {

        $useInfo = User::getUserInfo();

        $orderData = Order::where('id',$request->id)->first();
        $orderData->refundNot   = Order::ORDER_CHECK_OUT_ZERO;
        $orderData->updated_at  = time();
        $orderData->save();

        $refundsMsg = OrderRefunds::where('order_id',$request->id)
            ->where('guest_name',$useInfo->id)
            ->where('status',OrderRefunds::ORDER_CHECK_OUT_ZERO)
            ->first();

        $refundsMsg->status = OrderRefunds::ORDER_CHECK_OUT_THREE;
        $refundsMsg->updated_at = time();

        if ($refundsMsg->save()){
            return 'success';
        }

        return 'error';
    }

    /**
     * 申请续费
     */
    public static function applyRenewal($request)
    {
        try {
            $userInfo = User::getUserInfo();

            $orderId = $request->orderId;
            $orderData = Order::find($orderId);
            if (!$orderData) {
                return 'order_not_found';
            }

            $renewalData = OrderRenewal::where('order_id',$orderId)->first();
            if (!$renewalData){
                // 使用数据库事务
                DB::transaction(function () use ($orderData, $request, $userInfo, $orderId) {
                    $orderData->renewalNot = Order::ORDER_RENEW_TWO;
                    $orderData->save();

                    $orderRenewalArr = [
                        'order_id'          => $orderId,
                        'guest_id'          => $userInfo->id,
                        'institution_id'    => $request->institution_id,
                        'institution_type'  => $request->institution_type,
                        'room_number'       => $request->room_number,
                        'phone'             => $request->phone,
                        'remark'            => $request->remark,
                        'start_date'        => strtotime($request->start_date),
                        'end_date'          => strtotime($request->end_date),
                        'created_at'        => time(),
                    ];

                    OrderRenewal::insert($orderRenewalArr);
                });
                return true;
            }

            if ($renewalData->status == OrderRenewal::ORDER_RENEWAL_ZERO){
                return 'application_submitted';
            }

        } catch (\Exception $e) {
            // 记录错误日志或其他处理逻辑
            Log::error($e->getMessage());

            return false;
        }
    }

    /**
     * 取消申请续费
     */
    public static function offApplyRenewal($request)
    {

        try {
            $userInfo = User::getUserInfo();

            $orderId = $request->orderId;
            $orderData = Order::find($orderId);
            if (!$orderData) {
                return 'order_not_found';
            }

            // 使用数据库事务
            DB::transaction(function () use ($orderData, $request, $userInfo) {
                $orderData->renewalNot = Order::ORDER_RENEW_ZERO;
                $orderData->save();

                $renewalData = OrderRenewal::where('order_id',$orderData->id)->first();
                if ($renewalData){
                    $renewalData->delete();
                }

            });

            return true;
        } catch (\Exception $e) {
            // 记录错误日志或其他处理逻辑
            Log::error($e->getMessage());

            return false;
        }
    }
}


