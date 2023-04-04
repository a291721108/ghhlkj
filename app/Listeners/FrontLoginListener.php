<?php

namespace App\Listeners;

use App\Events\FrontLoginEvent;
use App\Models\User;


class FrontLoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\FrontLoginEvent $event
     * @return void
     */
    public function handle(FrontLoginEvent $event)
    {
        // 获取用户登录IP
        $id = $event->id;

        $data = [
            'last_login_time' => time(),
            'last_login_ip'   => getClientIp()
        ];

        User::where('id', $id)->update($data);
    }
}
