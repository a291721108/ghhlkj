<?php

namespace App\Models;


use Illuminate\Http\Request;

class Idea extends Common
{

    protected $table = 'gh_user_idea';

    protected $request;
    public $timestamps = true;

    /**
     * status
     */
    const  IDEA_STATUS_ONE = 1;  // 正常
    const  IDEA_STATUS_TWO = -1;  // 禁用

    const   IDEA_STATUS_MSG_ARRAY = [
        self::IDEA_STATUS_ONE      => "正常",
        self::IDEA_STATUS_TWO      => "禁用",
    ];

    /**
     * type
     */
    const  IDEA_TYPE_ONE = 1;  // 待通知
    const  IDEA_TYPE_TWO = 2;  // 已通知

    const   IDEA_TYPE_MSG_ARRAY = [
        self::IDEA_TYPE_ONE      => "待通知",
        self::IDEA_TYPE_TWO      => "已通知",
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


    /**
     * @param $page
     * @param $pageSize
     * @return array
     */
    public function getMsgPageList($page, $pageSize, $field = ['*'], $where = []): array
    {
        return $this->paginate($pageSize, $field, $page, 'page', $where);
    }
}
