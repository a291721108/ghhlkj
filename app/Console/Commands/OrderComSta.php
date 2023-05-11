<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Order;
use Illuminate\Console\Command;

class OrderComSta extends Command
{
    protected $signature = 'OrderComSta:ok';

    /**
     * 订单已完成状态修改
     * @return void
     */
    public function handle()
    {

        // 判断预约超期状态
        $orderBooking = Order::where('status',Order::ORDER_SYS_TYPE_ONE)->select()->get();

        dd($orderBooking);

        $orderBookingArr = [];
        $orderBookingRoomArr = [];

        foreach ($orderBooking as $v) {
            // 无押金预约
            if (time() > strtotime(date('Y-m-d H:i:s', $v->visitDate + 24 * 3600)) && $v->status == Order::ORDER_SYS_TYPE_FOUR) {
                $orderBookingArr[] = $v->id;
            }

            // 有押金预约
            if (time() > strtotime(date('Y-m-d H:i:s', $v->end_date)) && $v->status == Order::ORDER_SYS_TYPE_FOUR) {
                $orderBookingRoomArr[] = $v->id;
            }
        }

        if (!empty($orderBookingArr)) {
            Order::whereIn('id', $orderBookingArr)->update([
                'status' => Order::ORDER_SYS_TYPE_ZERO
            ]);
        }
        if (!empty($orderBookingRoomArr)) {
            Order::whereIn('id', $orderBookingRoomArr)->update([
                'status' => Order::ORDER_SYS_TYPE_ZERO
            ]);
        }

        // 预约状态修改
//        $order = Order::select()->get();
//        $orderArr = [];

//        foreach ($order as $v) {
//
//            if (time() > strtotime(date('Y-m-d H:i:s', $v->end_date)) || $v->refundNot == 0) {
//                $orderArr[] = $v->id;
//            }
//        }
//
//        if (!empty($orderArr)) {
//            Order::whereIn('id', $orderArr)->update([
//                'status' => Order::ORDER_SYS_TYPE_THERE
//            ]);
//        }

    }

}
