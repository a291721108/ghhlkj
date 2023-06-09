<?php

namespace App\Service\Admin;

use App\Models\Institution;
use App\Models\InstitutionAdmin;
use App\Models\InstitutionHome;
use App\Models\InstitutionHomeType;
use App\Models\Order;
use App\Models\OrderRefunds;
use App\Models\OrderRenewal;
use App\Models\User;
use App\Models\UserExt;
use App\Service\Common\FunService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderNotificationService
{

    /**
     * 订单列表
     */
    public static function getOrderList($request): array
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();

        $page     = $request->page ?? 1;
        $pageSize = $request->page_size ?? 20;

        $institutionId = Institution::where('admin_id',$adminInfo->id)->first();
        $orderInstitution = $institutionId->id;

        $query    = self::makeSearchWhere($request,$orderInstitution);

        // 获取分页数据
        $result = (new Order())->getMsgPageList($query, $page, $pageSize);

        // 处理特殊字段
        $result['data'] = self::dealReturnData($result['data'],$request->status);

        return $result;
    }

    /**
     * @param $request
     * @return mixed
     */
    protected static function makeSearchWhere($request,$orderInstitution)
    {
        $query = Order::where('institution_id', $orderInstitution)->orderBy('created_at', 'DESC');

        // 状态查询
        if (isset($request->status)) {
            if ($request->status == 1) {
                $pending = $query->where('status', Order::ORDER_SYS_TYPE_TWO)
                    ->where(function ($query) use ($request) {
                        $query->where('refundNot', OrderRefunds::ORDER_CHECK_OUT_TWO);
                        $query->orWhere('renewalNot', OrderRenewal::ORDER_RENEWAL_TWO);
                    })->pluck('id')
                    ->toArray();

                if (!empty($pending)) {
                    $pending = array_values($pending);
                    $query->whereIn('id', $pending);
                }
            }

            if ($request->status == 2) {

                $pending = $query->where('status', Order::ORDER_SYS_TYPE_TWO)
                    ->where(function ($query) use ($request) {
                        $query->where('refundNot', OrderRefunds::ORDER_CHECK_OUT_ZERO);
                        $query->orWhere('renewalNot', OrderRenewal::ORDER_RENEWAL_ZERO);
                    })->pluck('id')
                    ->toArray();

                if (!empty($pending)) {
                    $pending = array_values($pending);
                    $query->whereIn('id', $pending);
                }
                //                $query->where('status', Order::ORDER_SYS_TYPE_TWO)->where('refundNot', '0')->where('renewalNot', '0');
            }

            if ($request->status == 0) {

                $query->where('status', $request->status);
            }

            if ($request->status == 3) {

                $query->where('status', $request->status);
            }

            if ($request->status == 4) {

                $query->where('status', $request->status);
            }
        }

        return $query;
    }

    /**
     * 处理数组
     * @param $query
     * @param array $data
     * @return array
     */
    public static function dealReturnData($query,$request, array $data = [])
    {

        foreach ($query as $k => $v) {

            if ($v['refundNot'] == 2){
                $refunds = OrderRefunds::where('order_id',$v['id'])->first();
                $v['amount'] = $refunds->amount;
                $v['status'] = '待处理';
            } elseif ($v['renewalNot'] == 2 && $request == 1){
                $renewal = OrderRenewal::where('order_id',$v['id'])->first();
                $v['start_date']    = $renewal->start_date;
                $v['end_date']      = $renewal->end_date;
                $v['status']        = '待处理';
            } elseif($request == 0 && $request == ''){
                $v['amount'] = $v['amount_paid'];

            }

            // 处理回参
            $data[$k] = [
                'id'                 => $v['id'],
                'institution_id'     => Institution::getInstitutionId($v['institution_id']),
                'institution_type'   => InstitutionHomeType::getInstitutionTypeId($v['institution_type']),
                'total_amount'       => $v['total_amount'],
                'amount'             => $v['amount'],
                'amount_paid'        => $v['amount_paid'],
                'roomNum'            => InstitutionHome::getHomeIdBy($v['roomNum']),
                'contacts'           => $v['contacts'],
                'refundNot'          => $v['refundNot'],
                'renewalNot'         => $v['renewalNot'],
                'visitDate'          => ytdTampTime($v['visitDate']),
                'search_time'     => [
                    ytdTampTime($v['start_date']),
                    ytdTampTime($v['end_date'])
                ],
                'status'             => Order::INS_MSG_ARRAY[$v['status']] ?? $v['status'],
                'created_at'        => hourMinuteSecond($v['created_at']),
            ];
        }

        return $data;
    }
//-----------------------------------------------------------------------------------------------------
    /**
     * 同意预约  (不交定金)
     */
    public static function noDepositAgreed($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();
        $bookingId = $request->bookingId;

        $instututionId = Institution::where('admin_id',$adminInfo->id)->first();

        $bookIngMsg = Order::where('institution_id',$instututionId->id)
            ->where('status',Order::ORDER_SYS_TYPE_FOUR)
            ->where('id',$bookingId)
            ->first();

        $bookIngMsg->status  = Order::ORDER_SYS_TYPE_ONE;
        $bookIngMsg->roomNum = $request->roomID;
        $bookIngMsg->updated_at = time();

        if ($bookIngMsg->save()){

            $homeMsg = InstitutionHome::where('id',$request->roomID)->first();
            $homeMsg->instutution_status    = InstitutionHome::Home_SYS_STATUS_TWO;
            $homeMsg->updated_at            = time();
            $homeMsg->save();

            return "book_successfully";
        }

        return 'error';
    }

    /**
     * 同意退款
     */
    public static function agreeRefund($request)
    {

        $refundId = $request->refundId;

        $refundMsg = OrderRefunds::where('id',$refundId)
            ->where('status',OrderRefunds::ORDER_CHECK_OUT_ZERO)
            ->first();

        try {
            // 开启事务
            DB::beginTransaction();

            // 查询退款信息  执行一些数据库操作
            $refundMsg = OrderRefunds::where('order_id',$refundId)
                ->where('status',OrderRefunds::ORDER_CHECK_OUT_ZERO)
                ->first();
            $refundMsg->amount      = $request->amount;
            $refundMsg->refund_date = time();
            $refundMsg->status      = OrderRefunds::ORDER_CHECK_OUT_ONE;
            $refundMsg->updated_at  = time();
            $refundMsg->save();


            $orderMsg = Order::where('id',$refundId)->first();
            $orderMsg->refundNot    = Order::ORDER_CHECK_OUT_ONE;
            $orderMsg->status       = Order::ORDER_SYS_TYPE_THERE;
            $orderMsg->updated_at   = time();
            $orderMsg->save();


            // todo 退款流程   执行退款操作（此处省略具体的退款逻辑）  （待完善）

            // 提交事务
            DB::commit();

            return "successful_refund";
        } catch (\Exception $e) {
            // 发生异常时回滚事务
            DB::rollBack();

            // 处理异常，例如记录日志或返回错误信息
            return 'error';
//            return response()->json(['error' => '数据库操作失败'], 500);
        }
    }

    /**
     * 拒绝退款
     */
    public static function refusalRefund($request)
    {

        $refundId = $request->refundId;

        $refundMsg = OrderRefunds::where('id',$refundId)
            ->where('status',OrderRefunds::ORDER_CHECK_OUT_ZERO)
            ->first();

        $refundMsg->status = OrderRefunds::ORDER_CHECK_OUT_TWO;
        $refundMsg->updated_at = time();

        return $refundMsg->save();
    }

    /**
     * 同意续费
     */
    public static function agreeRenew($request)
    {
        $renewalId = $request->renewalId;

        $renewalMsg = OrderRenewal::where('id',$renewalId)->first();

        if ($renewalMsg->status == 1){
            return 'processed';
        }
        $renewalMsg->status        = OrderRenewal::ORDER_RENEWAL_ONE;
        $renewalMsg->updated_at    = time();

        if ($renewalMsg->save()){

            $orderMsg = Order::where('id',$renewalMsg->order_id)->first();
            $orderMsg->renewalNot = Order::ORDER_RENEW_ONE;
            $orderMsg->updated_at = time();
            $orderMsg->save();

            $homeMoney = InstitutionHomeType::where('id',$renewalMsg->institution_type)->first();

            //计算相差月数
            $data = getMonthDiff($renewalMsg->start_date,$renewalMsg->end_date);

            //  订单表创建预约数据
            $bookOrder = [
                'user_id'           => $renewalMsg->guest_id,
                'order_no'          => FunService::orderNumber(),
                'total_amount'      => $data * $homeMoney->home_price,
                'amount_paid'       => 0,
                'wait_pay'          => 0,
                'payment_method'    => 1,
                'institution_id'    => $renewalMsg->institution_id,
                'institution_type'  => $renewalMsg->institution_type,
                'roomNum'           => $renewalMsg->room_number,
                'discount_coupon'   => '无',
                'start_date'        => $renewalMsg->start_date,
                'end_date'          => $renewalMsg->end_date,
                'order_phone'       => $renewalMsg->phone,
                'contacts'          => UserExt::getMsgByUserName($renewalMsg->guest_id),
                'contacts_card'     => UserExt::getMsgByUserCard($renewalMsg->guest_id),
                'order_remark'      => $renewalMsg->remark,
                'renewalNot'        => Order::ORDER_RENEW_ZERO,
                'status'            => Order::ORDER_SYS_TYPE_ONE,
                'created_at'        => $renewalMsg->created_at

            ];

            if (Order::insert($bookOrder)){

                return "book_successfully";
            }

            return 'error';
        }

        return 'error';
    }

    /**
     * 续费详情
     */
    public static function agreeRenewDetail($request)
    {
        $orderId = $request->orderId;

        $renewData = OrderRenewal::where('order_id',$orderId)->where('status',OrderRenewal::ORDER_RENEWAL_ZERO)->first();
        if (!isset($renewData->status)){
            return 'processed';
        }

        $orderData = Order::where('id',$renewData->order_id)->first();

        return [
            'id'                => $renewData->id,
            'order_id'          => $renewData->order_id,
            'guest_id'          => User::getIdByname($renewData->guest_id),
            'institution_id'    => Institution::getInstitutionId($renewData->institution_id),
            'institution_type'  => InstitutionHomeType::getInstitutionTypeId($renewData->institution_type),
            'room_number'       => InstitutionHome::getHomeIdByName($renewData->room_number),
            'start_date'        => ytdTampTime($renewData->start_date),
            'end_date'          => ytdTampTime($renewData->end_date),
            'contacts'          => $orderData->contacts,
            'contacts_card'     => $orderData->contacts_card,
            'phone'             => $renewData->phone,
            'remark'            => $renewData->remark,
            'status'            => $renewData->status,
            'created_at'        => hourMinuteSecond($renewData->created_at)

        ];

    }

    /**
     * 订单详情
     */
    public static function getOrderDetail($request)
    {
        $orderId = $request->orderId;
        $order = Order::with('refunds', 'renewal')->find($orderId);
        $statusTwo = $order->status === Order::ORDER_SYS_TYPE_TWO;

        $created_at = '';
        $refundTime = '';
        $start_date = '';
        $end_date   = '';
        $remark = $order->order_remark;

        if ($order->status == Order::ORDER_SYS_TYPE_THERE){
            $refundData = OrderRefunds::where('status',1)->first();
            $amount = $refundData->amount;
            $refund_date = $refundData->refund_date;

        } elseif ($statusTwo) {
            if ($order->renewalNot === Order::ORDER_RENEW_TWO) {
                $renewal    = $order->renewal;
                $start_date = $renewal->start_date;
                $end_date   = $renewal->end_date;
                $remark     = $renewal->remark;
                $refundTime = $renewal->created_at;
            } elseif ($order->refundNot === Order::ORDER_CHECK_OUT_TWO) {
                $refunds    = $order->refunds;
                $refundTime = $refunds->created_at;
            }
        }

        $created_at = $created_at ?: $order->created_at;
        $start_date = $start_date ?: $order->start_date;
        $end_date   = $end_date ?: $order->end_date;
        $refundTime = $refundTime ?: '';

        return [
            'id'                => $order->id,
            'user_id'           => $order->user_id,
            'order_no'          => $order->order_no,
            'total_amount'      => $order->total_amount,
            'amount'            => $amount ?? '',
            'amount_paid'       => $order->amount_paid,
            'wait_pay'          => $order->wait_pay,
            'institution_id'    => $order->institution_id,
            'institution_type'  => InstitutionHomeType::getInstitutionTypeId($order->institution_type),
            'roomNum'           => InstitutionHome::getHomeIdBy($order->roomNum),
            'visitDate'         => ytdTampTime($order->visitDate),
            'start_date'        => ytdTampTime($start_date),
            'end_date'          => ytdTampTime($end_date),
            'order_phone'       => $order->order_phone,
            'order_remark'      => $remark,
            'refundNot'         => $order->refundNot,
            'refund_date'       => timestampTime($refund_date ?? '') ?? '',
            'renewalNot'        => $order->renewalNot,
            'contacts'          => $order->contacts,
            'contacts_card'     => $order->contacts_card,
            'status'            => $order->status,
            'created_at'        => hourMinuteSecond($created_at),
            'refundTime'        => hourMinuteSecond($refundTime),
            'updated_at'        => hourMinuteSecond($order->updated_at),
        ];
    }

}
