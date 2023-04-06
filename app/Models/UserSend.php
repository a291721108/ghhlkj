<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSend extends Model
{

    protected $table = 'gh_user_send';


    /**
     * 格式化时间
     * @param $value
     * @return false|int|string|null
     */
    public function fromDateTime($value)
    {
        return strtotime(parent::fromDateTime($value));
    }


}
