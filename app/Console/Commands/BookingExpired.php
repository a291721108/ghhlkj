<?php

namespace App\Console\Commands;


use App\Models\Booking;
use App\Models\Institution;
use App\Models\Order;
use Illuminate\Console\Command;

class BookingExpired extends Command
{
    protected $signature = 'bookingExpired:ok';

    /**
     * 修改项目状态
     * @return void
     */
    public function handle()
    {

        $query = Order::where('status', '=', Order::ORDER_SYS_TYPE_SIX)->select()->get();

        // 判断预约超期状态
        $arr = [];
        foreach ($query as $v) {
            if (time() > strtotime(date('Y-m-d H:i:s',$v -> check_in_date+24*3600)) && $v->status != Order::ORDER_SYS_TYPE_FOUR) {
                $arr[] = $v->id;
            }
        }

        if (!empty($arr)) {
            Order::whereIn('id', $arr)->update([
                'status' => Order::ORDER_SYS_TYPE_FOUR
            ]);
        }

    }

}
