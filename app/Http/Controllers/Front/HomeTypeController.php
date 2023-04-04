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
     * @title 房间类型详情
     * @description 获取所有房间列表详情
     * @method get
     * @url 47.92.82.25/api/organizationList
     *
     * @param tissue_id 必选 int 类型id
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"id":1,"institution_id":1,"home_type":"单人房","home_img":"https:\/\/picsum.photos\/seed\/picsum\/200\/300","home_pic":100,"home_size":30,"home_detal":"","home_facility":[{"id":1,"hotel_facilities":"凳子"},{"id":2,"hotel_facilities":"桌子"},{"id":3,"hotel_facilities":"大床"}],"status":1,"created_at":"2023-04-03 09:00"},{"id":2,"institution_id":1,"home_type":"单人房","home_img":"https:\/\/picsum.photos\/200\/300?grayscale","home_pic":120,"home_size":40,"home_detal":"","home_facility":[{"id":2,"hotel_facilities":"桌子"},{"id":3,"hotel_facilities":"大床"}],"status":1,"created_at":"2023-04-03 09:00"}]}
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
    public function tissueDetailPage(Request $request)
    {
        $this->validate($request, [
            'tissue_id'      => 'required|numeric',
        ]);

        $data = HomeTypeService::tissueDetailPage($request);

        if ($data) {
            return $this->success('success', '200', $data);
        }

        return $this->error('error');
    }


}
