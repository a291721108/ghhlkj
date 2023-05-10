<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BookingRoom;
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
        // 预约状态修改
        $booking = Booking::where('orderState', '=', Booking::BOOKING_SYS_TYPE_ONE)->select()->get();

        // 判断预约超期状态
        $bookingArr = [];
        foreach ($booking as $v) {

            if (time() > strtotime(date('Y-m-d H:i:s', $v->arrireDate + 24 * 3600)) && $v->orderState != Booking::BOOKING_SYS_TYPE_TWO) {
                $bookingArr[] = $v->id;
            }
        }

        if (!empty($bookingArr)) {
            Booking::whereIn('id', $bookingArr)->update([
                'orderState' => Booking::BOOKING_SYS_TYPE_ZERO
            ]);
        }

        // 订房状态修改
        $bookingRoom = BookingRoom::where('status', '=', BookingRoom::ROOM_SYS_TYPE_ONE)->select()->get();

        // 判断预约超期状态
        $roomArr = [];
        foreach ($bookingRoom as $v) {

            if ($v -> startDate > strtotime(date('Y-m-d H:i:s',$v -> startDate+24*3600)) && $v->status != BookingRoom::ROOM_SYS_TYPE_TWO) {
                $roomArr[] = $v->id;
            }
        }

        if (!empty($roomArr)) {
            BookingRoom::whereIn('id', $roomArr)->update([
                'status' => BookingRoom::ROOM_SYS_TYPE_ZERO
            ]);
        }

    }

}
