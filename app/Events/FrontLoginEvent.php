<?php

namespace App\Events;

class FrontLoginEvent extends Event
{

    /**
     * @var
     */
    public $id;


    /**
     * Create a new event instance.
     * 处理登录之后的后续操作
     * 写入用户openId 和 unionId
     * 更新用户最后一次登录地址和IP
     * @return void
     */
    public function __construct($obj)
    {
        $this->id        = $obj->id; // 用户ID

    }
}
