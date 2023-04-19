<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Common\BaseController;
use App\Service\Front\FriendService;
use Illuminate\Http\Request;

class FriendController extends BaseController
{

    /**
     * @catalog app端/亲友
     * @title 获取所有亲友状态
     * @description 获取所有亲友状态
     * @method get
     * @url 47.92.82.25/api/getRelativeStatus
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"id":1,"kinship_name":"子女","kinship_type":"正常"},{"id":2,"kinship_name":"父母","kinship_type":"正常"},{"id":3,"kinship_name":"配偶","kinship_type":"正常"},{"id":4,"kinship_name":"家人","kinship_type":"正常"},{"id":5,"kinship_name":"其他","kinship_type":"正常"}]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function getRelativeStatus()
    {

        $data = FriendService::getRelativeStatus();

        if (is_array($data)){
            return $this->success('success', 200, $data);
        }
        return $this->error('error');

    }

    /**
     * @catalog app端/亲友
     * @title 添加亲友
     * @description 添加亲友
     * @method post
     * @url 47.92.82.25/api/relativeStatusAdd
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function relativeStatusAdd(Request $request)
    {
        $this->validate($request, [
            'friend_name'       => 'required',
            'friend_card'       => 'required',
            'friend_kinship'    => 'required|numeric',
            'friend_tel'        => 'required|numeric',
        ]);

        $data = FriendService::relativeStatusAdd($request);

        if ($data) {
            return $this->success('success');
        }

        return $this->error('error');

    }

    /**
     * @catalog app端/亲友
     * @title 亲友列表
     * @description 亲友列表
     * @method get
     * @url 47.92.82.25/api/relativeStatusList
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"id":1,"user_id":{"id":1,"name":"admin","img":"https:\/\/picsum.photos\/id\/237\/200\/300","phone":"17821211068"},"friend_name":"周一","friend_card":"123456789","friend_kinship":[{"id":1,"kinship_name":"子女"}],"friend_tel":"123456789","friend_status":"正常","created_at":"2023-04-10 14:50:27","updated_at":""},{"id":2,"user_id":{"id":1,"name":"admin","img":"https:\/\/picsum.photos\/id\/237\/200\/300","phone":"17821211068"},"friend_name":"周二","friend_card":"123456789","friend_kinship":[{"id":2,"kinship_name":"父母"}],"friend_tel":"123456789","friend_status":"正常","created_at":"2023-04-10 14:50:27","updated_at":""}]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function relativeStatusList(Request $request)
    {

        $data = FriendService::relativeStatusList();

        return $this->success('success',200,$data);

    }

    public function relativeStatusDel(Request $request)
    {

        $data = FriendService::relativeStatusDel($request);

        return $this->success('success',200,$data);

    }
}
