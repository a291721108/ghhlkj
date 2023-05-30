<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class MessageList extends Model
{

    protected $table = 'gh_msg_list';

    public $timestamps = false;

    const MSG_LIST_ONE = 1;                     //1：微信，
    const MSG_LIST_TWO = 2;                     //2:短信，
    const MSG_LIST_THREE = 3;                   //3：小程序，
    const MSG_LIST_FOUR = 4;                    //4:后台客户端，
    const MSG_LIST_FIVE = 5;                    //5：虚拟电话，6：邮箱
    const MSG_LIST_SIX = 6;                    //6：邮箱


}
