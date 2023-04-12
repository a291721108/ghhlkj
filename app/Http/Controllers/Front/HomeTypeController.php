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
     * @method post
     * @url 47.92.82.25/api/homeTypeList
     *
     * @param id 必选 int 类型id
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"id":1,"home_type":"单人房","home_price":"1000.00","home_img":""},{"id":2,"home_type":"双人房","home_price":"2000.00","home_img":""},{"id":3,"home_type":"三人房","home_price":"3000.00","home_img":""}]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param home_type_name string 类型名称
     * @return_param status string 状态（1正常，-1禁用）
     *
     * @remark
     * @number 1
     */
    public function homeTypeList(Request $request)
    {
        $this->validate($request, [
            'id'      => 'required|numeric',
        ]);

        $data = HomeTypeService::homeTypeList($request);

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
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"id":1,"institution_id":"太原市小店区第1机构","home_type":"单人房","home_img":"","home_price":"1000.00","home_detail":"精装一居室套间，1室1厅1厨1卫1阳台，中式现代风格、环保装潢、安静明亮、智能门禁、智能家电、高档红木家具、星级酒店配套标准、","home_facility":"通风良好，空调，无障碍卫生间，无障碍地面，安坐扶靠","home_size":40,"status":"正常","created_at":false}]}
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
