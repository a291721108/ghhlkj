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
            'home_type'     => 'required|numeric',
            'home_price'    => 'required|numeric',
            'home_size'     => 'required|numeric',
            'home_facility' => 'required|numeric',
            'home_detail'   => 'required|numeric',
            'homeTypeImg'   => 'required|numeric',
            'homeNum'       => 'required|numeric',

        ]);

        $userInfo = RoomTypeService::addHomeType($request);

        if ($userInfo == 'success'){
            return $this->success('success');
        }
        return $this->error('error');
    }


}
