<?php

namespace App\Models;


use Illuminate\Http\Request;

class Order extends Common
{

    protected $table = 'gh_orders';


    public $timestamps = true;


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
