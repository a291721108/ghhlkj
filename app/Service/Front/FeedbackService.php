<?php

namespace App\Service\Front;

use App\Models\Idea;
use App\Models\IdeaType;
use App\Models\Order;
use App\Models\User;
use App\Service\Common\FunService;
use Illuminate\Support\Facades\Auth;

class FeedbackService
{
    /**
     * 意见反馈
     */
    public static function feedbackAdd($request)
    {
        $userInfo = User::getUserInfo();

        $data = [
            'user_id'           => $userInfo->id,
            'idea_type'         => $request->idea_type,
            'idea_content'      => $request->idea_content,
            'idea_img'          => $request->idea_img,
            'idea_status'       => Idea::IDEA_STATUS_ONE,
            'is_inform'         => Idea::IDEA_STATUS_ONE,
            'created_at'        => time(),
        ];

        return Idea::insert($data);
    }

    /**
     * 意见反馈类型
     */
    public static function feedbackType()
    {

        $query = IdeaType::where('type_status','>',IdeaType::IDEA_STATUS_TWO)->get()->toArray();

        foreach ($query as $k => $v) {
            $data[$k] = [
                'id'            => $v['id'],
                'type_name'     => $v['type_name'],
                'type_status'   => $v['type_status'],
                'created_at'    => formattingTime($v['created_at'])
            ];
        }

        return $data;

    }

    /**
     * 获取用户意见反馈
     */
    public static function getFeedbackList($request)
    {
        $userInfo = Auth::user();

        $page     = $request->page ?? 1;
        $pageSize = $request->page_size ?? 20;

        $where = [
            'user_id' => $userInfo->id,
        ];

        // 获取分页数据
        $result = (new Idea())->getMsgPageList($page, $pageSize,['*'], $where);

        foreach ($result['data'] as &$v) {
            // 处理回参
            $v['user_id']               = User::getUserInfoById($v['user_id']);
            $v['idea_type']             = IdeaType::getIdeaTypeById($v['idea_type']);
            $v['idea_status']           = Idea::IDEA_STATUS_MSG_ARRAY[$v['idea_status']];
            $v['is_inform']             = Idea::IDEA_TYPE_MSG_ARRAY[$v['is_inform']];
            $v['created_at']            = hourMinuteSecond(strtotime($v['created_at']));
            $v['updated_at']            = hourMinuteSecond(strtotime($v['updated_at']));
        }
        return $result;

    }

    /**
     * 反馈详情
     */
    public static function FeedbackList($request)
    {
        $result = Idea::where('id',$request->id)->get()->toArray();

        foreach ($result as $k => $v) {
            $data[$k] = [
                'id'            => $v['id'],
                'user_id'       => User::getUserInfoById($v['user_id']),
                'idea_type'     => IdeaType::getIdeaTypeById($v['idea_type']),
                'idea_content'  => $v['idea_content'],
                'idea_img'      => $v['idea_img'],
                'idea_status'   => Idea::IDEA_STATUS_MSG_ARRAY[$v['idea_status']],
                'is_inform'     => Idea::IDEA_TYPE_MSG_ARRAY[$v['is_inform']],
                'created_at'    => hourMinuteSecond(strtotime($v['created_at']))
            ];
        }

        return $data;
    }

}


