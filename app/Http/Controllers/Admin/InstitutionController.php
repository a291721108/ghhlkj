<?php

namespace App\Http\Controllers\Admin;

use App\Service\Admin\InstitutionService;
use Illuminate\Http\Request;
use App\Http\Controllers\Common\BaseController;


class InstitutionController extends BaseController
{

    /**
     * @catalog 商家端/机构
     * @title 机构添加
     * @description 机构添加
     * @method post
     * @url 39.105.183.79/admin/addInstitution
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param institution_name 必选 string 机构名称
     * @param institution_address 必选 string 机构地址
     * @param institution_img 必选 [] 机构图片
     * @param institution_detail 必选 string 机构详情
     * @param institution_tel 必选 int 机构电话
     * @param institution_type 必选 int 1民办2政府
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     *
     * @remark
     * @number 2
     */
    public function addInstitution(Request $request)
    {
        $this->validate($request, [
            'institution_name'      => 'required',
            'institution_address'   => 'required',
            'institution_img'       => 'required',
            'institution_detail'    => 'required',
            'institution_tel'       => 'required',
            'institution_type'      => 'required',
        ]);

        $userInfo = InstitutionService::addInstitution($request);

        if ($userInfo) {
            return $this->success('success', '200', []);
        }
        return $this->error('error');
    }

    /**
     * @catalog 商家端/机构
     * @title 机构编辑
     * @description 机构编辑
     * @method post
     * @url 39.105.183.79/admin/upInstitution
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param institutionId 必选 string 机构id
     * @param institution_name 必选 string 机构名称
     * @param institution_address 必选 string 机构地址
     * @param institution_img 必选 [] 机构图片
     * @param institution_detail 必选 string 机构详情
     * @param institution_tel 必选 int 机构电话
     * @param institution_type 必选 int 1民办2政府
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     *
     * @remark
     * @number 2
     */
    public function upInstitution(Request $request)
    {
        $this->validate($request, [
            'institutionId'      => 'required',
        ]);

        $userInfo = InstitutionService::upInstitution($request);

        if ($userInfo) {
            return $this->success('success', '200', []);
        }
        return $this->error('error');
    }

    /**
     * @catalog 商家端/机构
     * @title 机构查看
     * @description 机构查看
     * @method post
     * @url 39.105.183.79/admin/getInstitution
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     *
     * @remark
     * @number 2
     */
    public function getInstitution(Request $request)
    {

        $userInfo = InstitutionService::getInstitution($request);

        return $this->success('success', '200', $userInfo);

    }
}
