<?php

namespace App\Exceptions;

class MessageRemind
{
    //  任务提醒一
    const  ORDER_REMIND_ONE = 1;

    //消息推送 语言包
    const  WX_REMIND_MSG_TITLE = [
        self::ORDER_REMIND_ONE             => "预约看房",

    ];

    //消息推送 语言包
    const  WX_REMIND_MSG = [
        self::ORDER_REMIND_ONE             => "看房日期:{time}",
    ];
}
