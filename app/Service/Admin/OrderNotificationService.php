<?php

namespace App\Service\Admin;

use App\Models\Booking;
use App\Models\Institution;
use App\Models\InstitutionAdmin;
use App\Models\Order;

class OrderNotificationService
{

    /**
     * 同意预约  (不交定金)
     */
    public static function subscribeCheck($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();
        $bookingId = $request->bookingId;

        $bookIngMsg = Booking::where('institutionId',$adminInfo->admin_institution_id)
            ->where('orderState',Booking::BOOKING_SYS_TYPE_ONE)
            ->where('id',$bookingId)
            ->first();

        $bookIngMsg->orderState = Booking::BOOKING_SYS_TYPE_TWO;
        $bookIngMsg->updated_at = time();

        if ($bookIngMsg->save()){

            //  订单表创建预约数据
            $bookOrder = [
                'user_id'           => $bookIngMsg->userId,
                'order_no'          => $bookIngMsg->roomId,
                'institution_id'    => $bookIngMsg->institutionId,
                'institution_type'  => $bookIngMsg->typeId,
                'roomNum'           => $request->roomID,
                'discount_coupon'   => '无',
                'visitDate'         => $bookIngMsg->arrireDate,
                'contacts'          => $bookIngMsg->orderName,
                'contacts_card'     => $bookIngMsg->orderIDcard,
                'order_phone'       => $bookIngMsg->orderPhone,
                'order_remark'      => $bookIngMsg->remark,
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
