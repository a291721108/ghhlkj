<?php

namespace App\Http\Controllers\Admin;

use App\Service\Admin\RoomTypeService;
use Illuminate\Http\Request;
use App\Http\Controllers\Common\BaseController;

class RoomTypeController extends BaseController
{

    /**
     * @catalog 商家端/房间类型管理
     * @title 添加房间类型
     * @description 添加房间类型
     * @method post
     * @url 39.105.183.79/admin/addHomeType
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param home_type 必选 string 类型名称
     * @param home_price 必选 int 价格
     * @param home_size 必选 int 面积
     * @param home_facility 必选 string 房间设施
     * @param home_detail 必选 string 户型介绍
     * @param homeTypeImg 必选 【】 房间图片
     * @param homeNum 必选 【】 房间号
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
    public function addHomeType(Request $request)
    {
        $this->validate($request, [
            'home_type'     => 'required',
            'home_price'    => 'required',
            'home_size'     => 'required',
            'home_facility' => 'required',
            'home_detail'   => 'required',
            'homeTypeImg'   => 'required',
            'homeNum'       => 'required',
        ]);

        $userInfo = RoomTypeService::addHomeType($request);

        if ($userInfo == 'success'){
            return $this->success('success');
        }
        return $this->error('error');
    }

    /**
     * @catalog 商家端/房间类型管理
     * @title 获取房间类型列表
     * @description 获取房间类型列表
     * @method post
     * @url 39.105.183.79/admin/getHomeType
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
    public function getHomeType(Request $request)
    {

        $userInfo = RoomTypeService::getHomeType($request);

        if (is_array($userInfo)){
            return $this->success('success',200,$userInfo);
        }
        return $this->error('error');
    }

    /**
     * @catalog 商家端/房间类型管理
     * @title 根据id获取房间类型
     * @description 根据id获取房间类型
     * @method post
     * @url 39.105.183.79/admin/homeTypeInfo
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param homeTypeId 必选 string 类型名称
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"id":1,"institution_id":1,"home_type":"单人房","home_img":"https:\/\/picsum.photos\/seed\/picsum\/200\/300","home_price":"1500.00","home_size":40,"home_facility":"通风良好，空调，无障碍卫生间，无障碍地面，安坐扶靠","home_detail":"精装单人套间，1室1厅1厨1卫1阳台，中式现代风格、环保装潢、安静明亮、智能门禁、智能家电、高档红木家具、星级酒店配套标准、","status":1,"created_at":"","updated_at":""}]}
     *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     * @return_param token string token
     * @return_param id string 姓名
     * @return_param institution_id int 关联机构id
     * @return_param home_type string 类型名称
     * @return_param home_img 【】 图片
     * @return_param home_price string 价格
     * @return_param home_size string 面积
     * @return_param home_facility string 房间设施
     * @return_param home_detail string 户型介绍
     * @return_param status string 1正常-1禁用
     * @return_param home_num [] 房间号
     *
     * @remark
     * @number 2
     */
    public function homeTypeInfo(Request $request)
    {
        $this->validate($request, [
            'homeTypeId'     => 'required',
        ]);

        $userInfo = RoomTypeService::homeTypeInfo($request);

        if (is_array($userInfo)){
            return $this->success('success',200,$userInfo);
        }
        return $this->error('error');
    }

    /**
     * @catalog 商家端/房间类型管理
     * @title 编辑房间类型
     * @description 编辑获取房间类型
     * @method post
     * @url 39.105.183.79/admin/upHomeType
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param homeTypeId 必选 int 房间类型id
     * @param home_type 必选 string 类型名称
     * @param home_price 必选 string 价格
     * @param home_size 必选 string 面积
     * @param home_facility 必选 string 房间设施
     * @param home_detail 必选 string 户型介绍
     * @param homeTypeImg 必选 【】 图片
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"id":1,"institution_id":1,"home_type":"单人房","home_img":"https:\/\/picsum.photos\/seed\/picsum\/200\/300","home_price":"1500.00","home_size":40,"home_facility":"通风良好，空调，无障碍卫生间，无障碍地面，安坐扶靠","home_detail":"精装单人套间，1室1厅1厨1卫1阳台，中式现代风格、环保装潢、安静明亮、智能门禁、智能家电、高档红木家具、星级酒店配套标准、","status":1,"created_at":"","updated_at":""}]}
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
    public function upHomeType(Request $request)
    {
        $this->validate($request, [
            'homeTypeId'        => 'required',
            'home_type'         => 'required',
            'home_price'        => 'required',
            'home_size'         => 'required',
            'home_facility'     => 'required',
            'home_detail'       => 'required',
            'homeTypeImg'       => 'required',
        ]);

        $userInfo = RoomTypeService::upHomeType($request);

        if ($userInfo){
            return $this->success('success');
        }
        return $this->error('error');
    }
}
