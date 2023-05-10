<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //

        \App\Console\Commands\BookingExpired::class,

        // 意见反馈
        \App\Console\Commands\IdeaInform::class,

        // 订单已完成状态修改
        \App\Console\Commands\OrderComSta::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // 修改项目状态 每分钟监测一次
        $schedule->command('bookingExpired:ok')->everyMinute();

        // 意见反馈通知 每分钟监测一次
        $schedule->command('ideaInform:ok')->everyMinute();

        // 订单已完成状态修改 每分钟监测一次
        $schedule->command('OrderComSta:ok')->everyMinute();
    }
}
