<?php

namespace App\Service\Admin;

use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Institution;
use App\Models\InstitutionAdmin;
use App\Models\Order;

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
        $bookIngMsg->roomNum = $request->roomNum;
        $bookIngMsg->updated_at = time();

        if ($bookIngMsg->save()){

            //  订单表创建预约数据
//            $bookOrder = [
//                'user_id'           => $bookIngMsg->userId,
//                'order_no'          => $bookIngMsg->roomId,
//                'institution_id'    => $bookIngMsg->institutionId,
//                'institution_type'  => $bookIngMsg->typeId,
//                'roomNum'           => $request->roomID,
//                'discount_coupon'   => '无',
//                'visitDate'         => $bookIngMsg->arrireDate,
//                'contacts'          => $bookIngMsg->orderName,
//                'contacts_card'     => $bookIngMsg->orderIDcard,
//                'order_phone'       => $bookIngMsg->orderPhone,
//                'order_remark'      => $bookIngMsg->remark,
//                'status'            => Order::ORDER_SYS_TYPE_ONE,
//                'created_at'        => time()
//            ];

//            if (Order::insert($bookOrder)){

                return "book_successfully";
//            }

//            return 'error';
        }

        return 'error';
    }

    /**
     * 同意预约  (交定金)
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
