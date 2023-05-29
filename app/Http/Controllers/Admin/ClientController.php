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
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"user":"薛丁丁","info":{"id":2,"name":"xdd55","img":"https:\/\/www.ghhlkj.com\/.\/upload\/front\/20230428160403_4tCBg1aXPCIX95cc592da556a93ae88bf7b9722abcc1.png","phone":"15135345970","gender":"2"},"card":"142625199911044818","deal":"否"},{"user":"黄泽超","info":{"id":3,"name":"游客0003","img":"https:\/\/www.ghhlkj.com\/.\/upload\/qrcode\/my_img_default.png","phone":"13934120386","gender":"1"},"card":"140202199712142011","deal":"是"}]}
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
