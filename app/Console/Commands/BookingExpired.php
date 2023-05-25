<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class BookingExpired extends Command
{
    protected $signature = 'bookingExpired:ok';

    /**
     * 预约订单超时状态修改
     * @return void
     */
    public function handle()
    {
        // 判断预约超期状态
        $orderBooking = Order::where('status',Order::ORDER_SYS_TYPE_FOUR)->select()->get();

        $orderBookingArr = [];

        foreach ($orderBooking as $v) {

            // 无押金预约
            if (time() > strtotime(date('Y-m-d H:i:s', $v->visitDate + 24 * 3600)) && $v->status == Order::ORDER_SYS_TYPE_FOUR) {
                $orderBookingArr[] = $v->id;
            }
            if (time() > strtotime(date('Y-m-d H:i:s', $v->end_date) && $v->status == Order::ORDER_SYS_TYPE_FOUR)){
                $orderBookingArr[] = $v->id;
            }
        }

        if (!empty($orderBookingArr)) {
            Order::whereIn('id', $orderBookingArr)->update([
                'status' => Order::ORDER_SYS_TYPE_ZERO,
                'updated_at'    => time()

            ]);
        }

    }

}
