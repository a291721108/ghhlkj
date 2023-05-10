<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Common\BaseController;
use App\Service\Front\BookingRoomService;
use App\Service\Front\BookingService;
use Illuminate\Http\Request;

class BookingRoomController extends BaseController
{

    /**
     * @catalog app端/订房
     * @title 订房信息
     * @description 订房信息
     * @method post
     * @url 47.92.82.25/api/reservationInformation
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param institutionId 必选 int 机构id
     * @param typeId 必选 int 房间类型id
     * @param startDate 必选 string 入住日期
     * @param leaveDate 必选 string 离开日期
     * @param payment 必选 string 定价
     * @param orderPhone 必选 int 联系方式
     * @param remark 非必选 string 备注
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function reservationInformation(Request $request)
    {

        $this->validate($request, [
            'institutionId'     => 'required|numeric',
            'typeId'            => 'required|numeric',
            'startDate'         => 'required',
            'leaveDate'         => 'required',
            'payment'           => 'required',
            'orderPhone'        => 'required|numeric',
        ]);

        $data = BookingRoomService::reservationInformation($request);

        if ($data) {
            return $this->success('success');
        }

        return $this->error('error');
    }
}
