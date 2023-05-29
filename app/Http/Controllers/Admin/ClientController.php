<?php

namespace App\Http\Controllers\Admin;

use App\Service\Admin\ClientService;
use Illuminate\Http\Request;
use App\Http\Controllers\Common\BaseController;

class ClientController extends BaseController
{

    /**
     * @catalog 商家端/顾客管理
     * @title 获取顾客列表
     * @description 获取顾客列表
     * @method post
     * @url 39.105.183.79/admin/getClientList
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"id":1,"institution_num":"101","instutution_status":"启用","created_at":"2023-04-10 16:51:49"},{"id":2,"institution_num":"102","instutution_status":"已售","created_at":"2023-04-10 19:53:29"}]}
     *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     * @return_param token string token
     * @return_param admin_name string 姓名
     * @return_param admin_phone string 手机号
     * @return_param company_id string 公司ID
     *
     * @remark
     * @number 2
     */
    public function getClientList(Request $request)
    {

        $userInfo = ClientService::getClientList($request);

        if (is_array($userInfo)){
            return $this->success('success', '200', $userInfo);

        }
        return $this->error('error');
    }

}
