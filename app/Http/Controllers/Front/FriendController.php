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

}
