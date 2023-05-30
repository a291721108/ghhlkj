<?php

namespace App\Events;

class MsgPushEvent  extends Event
{
    /**
     * 发送人ID
     * @var
     */
    public $form;

    /**
     * 关联机构id
     * @var
     */
    public $institution_id;

    /**
     * 订单id
     * @var
     */
    public $order_id;

    /**
     * 名称
     * @var
     */
    public $name;


    /**
     * 内容
     * @var
     */
    public $content;


    /**
     * 发送时间
     * @var
     */
    public $time;

    /**
     * Create a new event instance.
     * 处理登录之后的后续操作
     * 写入用户openId 和 unionId
     * 更新用户最后一次登录地址和IP
     * @return void
     */
    public function __construct($obj)
    {
        $this->form             = $obj->form;      //发送人
        $this->institution_id   = $obj->institution_id;      //发送人
        $this->order_id         = $obj->order_id;   //接收人
        $this->name             = $obj->name;      //名称
        $this->content          = $obj->content;   //内容
        $this->time             = $obj->time;      //时间
    }
}
