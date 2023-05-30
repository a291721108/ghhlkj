<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\ExampleEvent::class => [
            \App\Listeners\ExampleListener::class,
        ],

        // 监听用户登录
        \App\Events\FrontLoginEvent::class => [
            // 处理用户登录后的业务逻辑
            \App\Listeners\FrontLoginListener::class,
        ],

        // 监听消息推送
        \App\Events\MsgPushEvent::class => [
            \App\Listeners\MsgPushListener::class,
        ],
    ];
}
