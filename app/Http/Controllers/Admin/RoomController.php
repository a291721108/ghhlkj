<?php

namespace App\Http\Controllers\Admin;

use App\Service\Admin\RoomService;
use Illuminate\Http\Request;
use App\Http\Controllers\Common\BaseController;


class RoomController extends BaseController
{

    /**
     * @catalog 商家端/房间管理
     * @title 获取机构房间列表
     * @description 获取机构房间列表
     * @method post
     * @url 39.105.183.79/admin/getInstitutionHomeList
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param typeId 必选 int 房间类型id
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
    public function getInstitutionHomeList(Request $request)
    {
        $this->validate($request, [
            'typeId' => 'required|numeric',
        ]);

        $userInfo = RoomService::getInstitutionHomeList($request);

        return $this->success('success', '200', $userInfo);
    }

    /**
     * @catalog 商家端/房间管理
     * @title 添加房间
     * @description 添加房间
     * @method post
     * @url 39.105.183.79/admin/addInstitutionHome
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param typeId 必选 int 房间类型id
     * @param homeArr 必选 [] 房间号
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
    public function addInstitutionHome(Request $request)
    {
        $this->validate($request, [
            'typeId'    => 'required|numeric',
            'homeArr'   => 'required'
        ]);

        $userInfo = RoomService::addInstitutionHome($request);

        if ($userInfo == 'success'){
            return $this->success($userInfo);
        }
        return $this->error($userInfo);
    }

}
