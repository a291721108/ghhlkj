<?php

namespace App\Service\Front;

use App\Models\Idea;
use App\Models\IdeaType;
use App\Models\Order;
use App\Models\User;
use App\Service\Common\FunService;

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
        $userInfo = User::getUserInfo();

        $query = IdeaType::where('type_status','>',IdeaType::IDEA_STATUS_TWO)->get()->toArray();

        foreach ($query as $k => $v){
            $data[$k] = [
                'id' => $v['id'],
                'type_name'=> $v['type_name'],
                'type_status'   => $v['type_status'],
                'created_at'    => formattingTime($v['created_at'])
            ];
        }

        return $data;

    }
}


