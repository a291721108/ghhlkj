<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Common\BaseController;
use App\Service\Front\BookingService;
use App\Service\Front\OrganizationService;
use Illuminate\Http\Request;

class BookingController extends BaseController
{

    /**
     * @catalog app端/订单
     * @title 预约信息
     * @description 预约信息
     * @method post
     * @url 47.92.82.25/api/agencyAppointment
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param institution_id 必选 int 机构id
     * @param home_type_id 必选 int 房间类型id
     * @param check_in_date 必选 string 看房日期
     * @param contacts 必选 string 联系人
     * @param contact_way 必选 int 联系方式
     * @param remark 必选 string 备注
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function agencyAppointment(Request $request)
    {

        $this->validate($request, [
            'institution_id'    => 'required|numeric',
            'home_type_id'      => 'required|numeric',
            'check_in_date'     => 'required|numeric',
            'contacts'          => 'required',
            'contact_way'       => 'required|numeric',
        ]);

        $data = BookingService::agencyAppointment($request);

        if ($data) {
            return $this->success('success');
        }

        return $this->error('error');
    }

    /**
     * @catalog app端/订单
     * @title 预约列表
     * @description 预约列表
     * @method get
     * @url 47.92.82.25/api/reservationList
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"user_id":1,"user_name":"admin","institution_id":"太原市小店区第1机构","home_type_id":"单人房","check_in_date":"2023-04-12","contacts":"周一飞","contact_way":"17821211068","remark":"朝阳","status":"成功","created_at":"2023-04-13 10:04:40"},{"user_id":1,"user_name":"admin","institution_id":"太原市小店区第1机构","home_type_id":"双人房","check_in_date":"2023-04-12","contacts":"周一飞","contact_way":"17821211068","remark":"","status":"成功","created_at":"2023-04-13 10:04:40"}]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param user_id int 用户id
     * @return_param user_name string 用户名称
     * @return_param institution_id string 机构名称
     * @return_param home_type_id stirng 房间类型
     * @return_param check_in_date time 看房日期
     * @return_param contacts string 联系人
     * @return_param contact_way int 联系方式
     * @return_param remark string 备注
     * @return_param status int 状态（1成功，2取消，3已过期，-1删除）
     * @return_param created_at time 创建时间
     *
     * @remark
     * @number 1
     */
    public function reservationList(Request $request)
    {

        $data = BookingService::reservationList($request);

        if ($data) {
            return $this->success('success',200,$data);
        }

        return $this->error('error');
    }
}
