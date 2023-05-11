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

        // 预约状态修改
        $order = Order::select()->get();
        $orderArr = [];

        foreach ($order as $v) {

            if (time() > strtotime(date('Y-m-d H:i:s', $v->end_date)) && $v->refundNot != 0) {
                $orderArr[] = $v->id;
            }
        }

        if (!empty($orderArr)) {
            Order::whereIn('id', $orderArr)->update([
                'status' => Order::ORDER_SYS_TYPE_THERE
            ]);
        }

    }

}
