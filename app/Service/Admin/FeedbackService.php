<?php

namespace App\Service\Admin;

use App\Models\ClientIdea;
use App\Models\Idea;
use App\Models\IdeaType;
use App\Models\Institution;
use App\Models\InstitutionAdmin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FeedbackService
{
    /**
     * 意见反馈
     */
    public static function clientFeedbackAdd($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();

        $data = [
            'admin_id'          => $adminInfo->id,
            'idea_type'         => $request->idea_type,
            'idea_content'      => $request->idea_content,
            'idea_img'          => $request->idea_img,
            'idea_status'       => ClientIdea::CLIENT_IDEA_STATUS_ONE,
            'is_inform'         => ClientIdea::CLIENT_IDEA_TYPE_ONE,
            'created_at'        => time(),
        ];

        return ClientIdea::insert($data);
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
        $adminInfo = InstitutionAdmin::getAdminInfo();

        $page     = $request->page ?? 1;
        $pageSize = $request->page_size ?? 20;

        $where = [
            'admin_id' => $adminInfo->id,
        ];

        // 获取分页数据
        $result = (new ClientIdea())->getMsgPageList($page, $pageSize,['*'], $where);

        foreach ($result['data'] as &$v) {
            // 处理回参
            $v['admin_id']              = Institution::getClientAdminByName($v['admin_id']);
            $v['idea_type']             = IdeaType::getIdeaTypeById($v['idea_type']);
            $v['idea_status']           = ClientIdea::CLIENT_IDEA_STATUS_MSG_ARRAY[$v['idea_status']];
            $v['is_inform']             = ClientIdea::CLIENT_IDEA_TYPE_MSG_ARRAY[$v['is_inform']];
            $v['created_at']            = hourMinuteSecond(strtotime($v['created_at']));
            $v['updated_at']            = hourMinuteSecond(strtotime($v['updated_at']));
        }
        return $result;

    }

    /**
     * 反馈详情
     */
    public static function clientFeedbackList($request)
    {
        $result = ClientIdea::where('id',$request->id)->get()->toArray();

        foreach ($result as $k => $v) {
            $data[$k] = [
                'id'            => $v['id'],
                'admin_id'       => Institution::getClientAdminByName($v['admin_id']),
                'idea_type'     => IdeaType::getIdeaTypeById($v['idea_type']),
                'idea_content'  => $v['idea_content'],
                'idea_img'      => $v['idea_img'],
                'idea_status'   => ClientIdea::CLIENT_IDEA_STATUS_MSG_ARRAY[$v['idea_status']],
                'is_inform'     => ClientIdea::CLIENT_IDEA_TYPE_MSG_ARRAY[$v['is_inform']],
                'created_at'    => hourMinuteSecond(strtotime($v['created_at']))
            ];
        }

        return $data;
    }

}


