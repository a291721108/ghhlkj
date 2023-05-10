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

    /***
     * showodc
     * @catalog app端/订房
     * @title 获取单条订房信息
     * @description 获取单条订房信息
     * @method post
     * @url 47.92.82.25/api/getBookRoomOneMsg
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param bookingRoomId 必选 int 预约信息id
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":{"id":1,"orderName":"周一飞","orderIDcard":"142322199806221012","orderPhone":"17821211068","institution_name":"太原市小店区第1机构","typeId":[{"id":1,"home_type":"单人房","home_price":"1500.00","home_detail":"精装单人套间，1室1厅1厨1卫1阳台，中式现代风格、环保装潢、安静明亮、智能门禁、智能家电、高档红木家具、星级酒店配套标准、","home_img":"https:\/\/picsum.photos\/seed\/picsum\/200\/300"}],"arrireDate":"","remark":"今天我不去看房子","roomId":"GH20230509091300119","orderState":"订房","created_at":"2023-05-09 09:14:07"}}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param user_name int 用户名称
     * @return_param institution_name string 机构名称
     * @return_param home_type_name string 房间类型
     * @return_param check_in_date stirng 看房日期
     * @return_param contacts time 联系人
     * @return_param contact_way string 联系方式
     * @return_param remark int 备注
     * @return_param status string 状态
     * @return_param created_at time 创建时间
     *
     * @remark
     * @number 1
     */
    public function getBookRoomOneMsg(Request $request)
    {
        $this->validate($request, [
            'bookingRoomId'    => 'required|numeric',
        ]);

        $data = BookingRoomService::getBookRoomOneMsg($request);

        if ($data) {
            return $this->success('success',200,$data);
        }

        return $this->error('error');
    }
}
