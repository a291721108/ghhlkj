<?php

namespace App\Console\Commands;


use App\Models\Booking;
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

        $query = Booking::where('status', '>', Booking::BOOKING_SYS_TYPE_FOUR)->select()->get();

        // 判断预约超期状态
        $arr = [];
        foreach ($query as $v) {
            if (time() > strtotime(date('Y-m-d H:i:s',$v -> check_in_date+24*3600)) && $v->status != Booking::BOOKING_SYS_TYPE_FOUR) {
                $arr[] = $v->id;
            }
        }

        if (!empty($arr)) {
            Booking::whereIn('id', $arr)->update([
                'status' => Booking::BOOKING_SYS_TYPE_THERE
            ]);
        }

    }

}
