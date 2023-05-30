<?php

namespace App\Models;


use Illuminate\Http\Request;

class ClientIdea extends Common
{

    protected $table = 'gh_client_idea';

    protected $request;
    public $timestamps = true;

    /**
     * status
     */
    const  CLIENT_IDEA_STATUS_ONE = 1;  // 正常
    const  CLIENT_IDEA_STATUS_TWO = -1;  // 禁用

    const   CLIENT_IDEA_STATUS_MSG_ARRAY = [
        self::CLIENT_IDEA_STATUS_ONE      => "正常",
        self::CLIENT_IDEA_STATUS_TWO      => "禁用",
    ];

    /**
     * type
     */
    const  CLIENT_IDEA_TYPE_ONE = 1;  // 待通知
    const  CLIENT_IDEA_TYPE_TWO = 2;  // 已通知

    const   CLIENT_IDEA_TYPE_MSG_ARRAY = [
        self::CLIENT_IDEA_TYPE_ONE      => "待通知",
        self::CLIENT_IDEA_TYPE_TWO      => "已通知",
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
