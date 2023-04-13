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


}
