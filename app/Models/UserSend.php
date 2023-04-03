<?php
/**
 * Created by LJL.
 * Date: 2022/3/15
 * Time: 16:55
 */

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
