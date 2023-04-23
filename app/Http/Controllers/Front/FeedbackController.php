<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Common\BaseController;
use App\Service\Front\FeedbackService;
use App\Service\Front\OrderService;
use Illuminate\Http\Request;

class FeedbackController extends BaseController
{

    /**
     * @catalog app端/意见反馈
     * @title 意见反馈
     * @description 意见反馈
     * @method post
     * @url 47.92.82.25/api/feedbackAdd
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param idea_type 必选 int 反馈类型id
     * @param idea_content 必选 string 反馈内容
     * @param idea_img 非必选 string 附加图片
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function feedbackAdd(Request $request)
    {

        $this->validate($request, [
            'idea_type'         => 'required|numeric',
            'idea_content'      => 'required',
        ]);

        $data = FeedbackService::feedbackAdd($request);

        if ($data) {
            return $this->success('success');
        }

        return $this->error('error');
    }

    /**
     * @catalog app端/意见反馈
     * @title 意见反馈类型
     * @description 意见反馈类型
     * @method get
     * @url 47.92.82.25/api/feedbackType
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"id":1,"type_name":"实名认证","type_status":1,"created_at":"1970-01-01 08"},{"id":2,"type_name":"页面报错\/慢\/卡顿","type_status":1,"created_at":"1970-01-01 08"},{"id":3,"type_name":"咨询问题","type_status":1,"created_at":"1970-01-01 08"},{"id":4,"type_name":"其他","type_status":1,"created_at":"1970-01-01 08"}]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function feedbackType()
    {

        $data = FeedbackService::feedbackType();

        if (is_array($data)) {
            return $this->success('success',200,$data);
        }

        return $this->error('error');
    }

}
