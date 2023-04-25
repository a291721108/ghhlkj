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

    /**
     * @catalog app端/意见反馈
     * @title 获取用户意见反馈
     * @description 获取用户意见反馈
     * @method post
     * @url 47.92.82.25/api/getFeedbackList
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param page 必选 int 页
     * @param page_size 必选 int 数据
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":{"total":2,"current_page":"1","page_size":"5","pages":1,"data":[{"id":2,"user_id":{"id":2,"name":"游客111","img":"https:\/\/picsum.photos\/id\/237\/200\/300","phone":"15135345970"},"idea_type":"实名认证","idea_content":"周一飞","idea_img":"123","idea_status":"正常","is_inform":"已通知","created_at":"2023-04-20 11:53:56","updated_at":"2023-04-20 14:59:01"},{"id":1,"user_id":{"id":2,"name":"游客111","img":"https:\/\/picsum.photos\/id\/237\/200\/300","phone":"15135345970"},"idea_type":"实名认证","idea_content":"建议就是说这个建议","idea_img":"","idea_status":"正常","is_inform":"已通知","created_at":false,"updated_at":"2023-04-20 14:59:01"}]}}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param total int 总条数
     * @return_param current_page int 当前页
     * @return_param page_size int 每页请求条数
     * @return_param id int id
     * @return_param user_id [] 用户信息
     * @return_param idea_type string 反馈类型
     * @return_param idea_content string 反馈内容
     * @return_param idea_img string 图片
     * @return_param idea_status string 反馈状态
     * @return_param is_inform float 是否通知
     * @return_param created_at float 创建时间
     * @return_param updated_at float 修改时间
     *
     * @remark
     * @number 1
     */
    public function getFeedbackList(Request $request)
    {
        $this->validate($request, [
            'page'      => 'required|numeric',
            'page_size' => 'required|numeric',
        ]);

        $data = FeedbackService::getFeedbackList($request);

        if ($data) {
            return $this->success('success', '200', $data);
        }

        return $this->error('error');
    }

    /**
     * @catalog app端/意见反馈
     * @title 反馈详情
     * @description 反馈详情
     * @method post
     * @url 47.92.82.25/api/FeedbackList
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param id 必选 int 反馈数据id
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"id":1,"user_id":{"id":2,"name":"游客111","img":"https:\/\/picsum.photos\/id\/237\/200\/300","phone":"15135345970"},"idea_type":"实名认证","idea_content":"建议就是说这个建议","idea_img":"","idea_status":"正常","is_inform":"已通知","created_at":false}]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param id int id
     * @return_param user_id [] 用户信息
     * @return_param idea_type string 反馈类型
     * @return_param idea_content string 反馈内容
     * @return_param idea_img string 图片
     * @return_param idea_status string 反馈状态
     * @return_param is_inform float 是否通知
     * @return_param created_at float 创建时间
     * @return_param updated_at float 修改时间
     *
     * @remark
     * @number 1
     */
    public function FeedbackList(Request $request)
    {

        $data = FeedbackService::FeedbackList($request);

        if ($data) {
            return $this->success('success', '200', $data);
        }

        return $this->error('error');
    }
}
