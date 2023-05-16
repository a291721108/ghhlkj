<?php

namespace App\Service\Admin;

use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Institution;
use App\Models\InstitutionAdmin;
use App\Models\Order;
use App\Models\OrderRefunds;
use Illuminate\Support\Facades\DB;

class OrderNotificationService
{

    /**
     * 同意预约  (不交定金)
     */
    public static function noDepositAgreed($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();
        $bookingId = $request->bookingId;

        $bookIngMsg = Order::where('institution_id',$adminInfo->admin_institution_id)
            ->where('status',Order::ORDER_SYS_TYPE_FOUR)
            ->where('id',$bookingId)
            ->first();

        $bookIngMsg->status  = Order::ORDER_SYS_TYPE_ONE;
        $bookIngMsg->roomNum = $request->roomID;
        $bookIngMsg->updated_at = time();

        if ($bookIngMsg->save()){

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
     * 同意续费
     */
    public static function depositAgreed($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();
        $bookingRoomId = $request->bookingRoomId;

        $bookIngRoomMsg = BookingRoom::where('institutionId',$adminInfo->admin_institution_id)
            ->where('status',BookingRoom::ROOM_SYS_TYPE_ONE)
            ->where('id',$bookingRoomId)
            ->first();

        $bookIngRoomMsg->status         = BookingRoom::ROOM_SYS_TYPE_TWO;
        $bookIngRoomMsg->updated_at     = time();

        if ($bookIngRoomMsg->save()){

            //  订单表创建预约数据
            $bookOrder = [
                'user_id'           => $bookIngRoomMsg->userId,
                'order_no'          => $bookIngRoomMsg->roomId,
                'institution_id'    => $bookIngRoomMsg->institutionId,
                'institution_type'  => $bookIngRoomMsg->typeId,
                'roomNum'           => $request->roomID,
                'discount_coupon'   => '无',
                'start_date'        => $bookIngRoomMsg->startDate,
                'end_date'          => $bookIngRoomMsg->leaveDate,
                'amount_paid'       => $bookIngRoomMsg->payment,
                'contacts'          => $bookIngRoomMsg->orderName,
                'contacts_card'     => $bookIngRoomMsg->orderIDcard,
                'order_phone'       => $bookIngRoomMsg->orderPhone,
                'order_remark'      => $bookIngRoomMsg->remark,
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


}
