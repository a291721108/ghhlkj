<?php

namespace App\Listeners;

use App\Events\MsgPushEvent;
use App\Exceptions\MessageRemind;
use App\Models\MessageList;

class MsgPushListener
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
    public function handle(MsgPushEvent $event)
    {

        $data = [
            'form'              => $event->form,
            'institution_id'    => $event->institution_id,
            'order_id'          => $event->order_id,
            'name'              => MessageRemind::WX_REMIND_MSG_TITLE[$event->name],
            'content'           => $event->content,
            'time'              => $event->time ?? time(),
            'created_at'        => time()
        ];

        MessageList::insert($data);


    }
}
