<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Common\BaseController;
use App\Service\Front\HomeTypeService;
use App\Service\Front\OrganizationService;
use Illuminate\Http\Request;

class HomeTypeController extends BaseController
{

    /**
     * @catalog app端/房间类型
     * @title 房间类型列表
     * @description 获取房间类型
     * @method get
     * @url 47.92.82.25/api/homeTypeList
     *
     * @param
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"id":1,"home_type_name":"单人房","status":1,"created_at":"","updated_at":""},{"id":2,"home_type_name":"双人房","status":1,"created_at":"","updated_at":""},{"id":3,"home_type_name":"三人房","status":1,"created_at":"","updated_at":""}]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param home_type_name string 类型名称
     * @return_param status string 状态（1正常，-1禁用）
     *
     * @remark
     * @number 1
     */
    public function homeTypeList()
    {

        $data = HomeTypeService::homeTypeList();

        if ($data) {
            return $this->success('success', '200', $data);
        }

        return $this->error('error');
    }

    /**
     * @catalog app端/房间类型
     * @title 通过id获取机构类型详情
     * @description 通过id获取机构类型详情
     * @method post
     * @url 47.92.82.25/api/organizationList
     *
     * @param id 必选 int 类型id
     *
     * @return
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param institution_id int 机构id
     * @return_param home_type string 房间类型
     * @return_param home_img img 房间图片
     * @return_param home_pic floot 房间价格
     * @return_param home_size ing 房间大小
     * @return_param home_detal string 房间详情
     * @return_param home_facility [] 房间设备
     * @return_param status string 状态（1正常，-1禁用）
     *
     * @remark
     * @number 1
     */
    public function organizationTypeDetails(Request $request)
    {
        $this->validate($request, [
            'id'      => 'required|numeric',
        ]);

        $data = HomeTypeService::organizationTypeDetails($request);

        if ($data) {
            return $this->success('success', '200', $data);
        }

        return $this->error('error');
    }


}
