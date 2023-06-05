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
        $result['data'] = self::dealReturnData($result['data']);

        return $result;
    }

    /**
     * @param $request
     * @return mixed
     */
    protected static function makeSearchWhere($request,$orderInstitution)
    {
        $query = Order::where('institution_id', $orderInstitution);

        // 状态查询
        if (isset($request->status)) {
            if ($request->status == 1) {

                $query->where('status', $request->status)->where('refundNot', '1')->orWhere('renewalNot', '1');
            }
            if ($request->status == 2) {

                $query->where('status', $request->status)->where('refundNot', '2')->orWhere('renewalNot', '2');
            }
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
            if ($v['refundNot'] == 2){
                $refunds = OrderRefunds::where('order_id',$v['id'])->first();
                $v['amount'] = $refunds->amount;
                $v['refundNot'] = '待处理';
            }
            if ($v['renewalNot'] == 2){
                $v['renewalNot'] = '待处理';
            }
            // 处理回参
            $data[$k] = [
                'id'                 => $v['id'],
                'institution_id'     => Institution::getInstitutionId($v['institution_id']),
                'institution_type'   => InstitutionHomeType::getInstitutionTypeId($v['institution_type']),
                'total_amount'       => $v['total_amount'],
                'amount_paid'        => $v['amount_paid'],
                'roomNum'            => InstitutionHome::getHomeIdBy($v['roomNum']),
                'contacts'           => $v['contacts'],
                'amount'             => $v['amount'],
                'refundNot'          => $v['refundNot'],
                'renewalNot'         => $v['renewalNot'],
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
            $refundMsg = OrderRefunds::where('id',$refundId)
                ->where('status',OrderRefunds::ORDER_CHECK_OUT_ZERO)
                ->first();
            $refundMsg->amount      = $request->amount;
            $refundMsg->refund_date = time();
            $refundMsg->status      = OrderRefunds::ORDER_CHECK_OUT_ONE;
            $refundMsg->updated_at  = time();
            $refundMsg->save();


            $orderMsg = Order::where('id',$refundMsg->order_id)->first();
            $orderMsg->refundNot    = Order::ORDER_CHECK_OUT_ONE;
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
                'start_date'        => $renewalMsg->startDate,
                'end_date'          => $renewalMsg->leaveDate,
                'order_phone'       => User::getUserInfoById($renewalMsg->id)['phone'],
                'contacts'          => User::getUserInfoById($renewalMsg->id)['name'],
                'contacts_card'     => UserExt::getMsgByUserCard($renewalMsg->guest_id),
                'order_remark'      => $renewalMsg->remark,
                'status'            => Order::ORDER_SYS_TYPE_ONE,
                'created_at'        => time()
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

        $orderData = Order::where('id',$orderId)->first();
        if ($orderData->refundNot == 1){
            $refunds = OrderRefunds::where('order_id',$orderData->id)->first();
            $amount = $refunds->amount;
            $refund_date = $refunds->refund_date;
        }
        return [
            'id'                => $orderData->id,
            'user_id'           => $orderData->user_id,
            'order_no'          => $orderData->order_no,
            'total_amount'      => $orderData->total_amount,
            'amount_paid'       => $orderData->amount_paid,
            'wait_pay'          => $orderData->wait_pay,
            'institution_id'    => $orderData->institution_id,
            'institution_type'  => InstitutionHomeType::getInstitutionTypeId($orderData->institution_type),
            'roomNum'           => InstitutionHome::getHomeIdBy($orderData->roomNum),
            'visitDate'         => ytdTampTime($orderData->visitDate),
            'start_date'        => ytdTampTime($orderData->start_date),
            'end_date'          => ytdTampTime($orderData->end_date),
            'order_phone'       => $orderData->order_phone,
            'order_remark'      => $orderData->order_remark,
            'refundNot'         => $orderData->refundNot,
            'amount'            => $amount ?? '',
            'refund_date'       => timestampTime($refund_date ?? '') ?? '',
            'renewalNot'        => $orderData->renewalNot,
            'contacts'          => $orderData->contacts,
            'contacts_card'     => $orderData->contacts_card,
            'status'            => $orderData->status,
            'created_at'        => hourMinuteSecond(strtotime($orderData->created_at)),
            'updated_at'        => hourMinuteSecond(strtotime($orderData->updated_at)),

        ];
    }

}
