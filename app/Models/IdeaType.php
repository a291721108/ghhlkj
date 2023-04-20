<?php

namespace App\Models;


use Illuminate\Http\Request;

class IdeaType extends Common
{

    protected $table = 'gh_user_idea_type';

    protected $request;
    public $timestamps = true;

    /**
     * type
     */
    const  IDEA_STATUS_ONE = 1;  // 正常
    const  IDEA_STATUS_TWO = -1;  // 禁用

    const   IDEA_STATUS_MSG_ARRAY = [
        self::IDEA_STATUS_ONE      => "正常",
        self::IDEA_STATUS_TWO      => "禁用",
    ];

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
