<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Common\BaseController;
use App\Service\Front\MerchantService;
use Illuminate\Http\Request;

class MerchantController extends BaseController
{

    /**
     * @catalog app端/机构
     * @title 获取机构电话
     * @description 获取机构电话
     * @method get
     * @url 47.92.82.25/api/getInstitutionTel
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param institution 必选 int 机构id
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":{"tel":"12345678912"}}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param id string id
     * @return_param img string 图片路径
     *
     * @remark
     * @number 1
     */
    public function getInstitutionTel(Request $request)
    {
        $this->validate($request, [
            'institution'      => 'required|numeric',
        ]);

        $data = MerchantService::getInstitutionTel($request);

        if ($data) {
            return $this->success('success', '200', $data);
        }

        return $this->error('error');
    }



}
