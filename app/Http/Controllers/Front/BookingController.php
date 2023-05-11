<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Common\BaseController;
use App\Service\Front\BookingService;
use App\Service\Front\OrganizationService;
use Illuminate\Http\Request;

class BookingController extends BaseController
{

    /**
     * @catalog app端/预约
     * @title 预约信息
     * @description 预约信息
     * @method post
     * @url 47.92.82.25/api/agencyAppointment
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param institutionId 必选 int 机构id
     * @param typeId 必选 int 房间类型id
     * @param arrireDate 必选 string 看房日期
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
    public function agencyAppointment(Request $request)
    {

        $this->validate($request, [
            'institutionId'    => 'required|numeric',
            'typeId'           => 'required|numeric',
            'arrireDate'       => 'required',
            'orderPhone'        => 'required|numeric',
        ]);

        $data = BookingService::agencyAppointment($request);

        if ($data) {
            return $this->success('success');
        }

        return $this->error('error');
    }

    /**
     * @catalog app端/预约
     * @title 预约列表
     * @description 预约列表
     * @method get
     * @url 47.92.82.25/api/reservationList
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"id":1,"userId":1,"orderName":"周一飞","orderPhone":"17821211068","orderIDcard":"142322199806221012","institutionId":"太原市小店区第1机构","typeId":[{"id":1,"home_type":"单人房","home_price":"1500.00","home_detail":"精装单人套间，1室1厅1厨1卫1阳台，中式现代风格、环保装潢、安静明亮、智能门禁、智能家电、高档红木家具、星级酒店配套标准、","home_img":"https:\/\/picsum.photos\/seed\/picsum\/200\/300"}],"arrireDate":"","orderState":"订房","roomId":"GH20230509091300119","remark":"今天我不去看房子","created_at":"2023-05-10 09:02:00"}]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param id int id
     * @return_param userId int 用户id
     * @return_param institutionId string 机构名称
     * @return_param typeId [] 房间类型
     * @return_param arrireDate time 看房日期
     * @return_param orderName string 联系人
     * @return_param orderPhone int 联系方式
     * @return_param remark string 备注
     * @return_param roomId string 订单编号
     * @return_param orderState int 状态（ 1预约看房  2预约成功 0取消）
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

    /**
     * @catalog app端/预约
     * @title 获取单条预约信息
     * @description 获取单条预约信息
     * @method post
     * @url 47.92.82.25/api/getBookOneMsg
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param bookingId 必选 int 预约信息id
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
    public function getBookOneMsg(Request $request)
    {
        $this->validate($request, [
            'bookingId'    => 'required|numeric',
        ]);

        $data = BookingService::getBookOneMsg($request);

        if ($data) {
            return $this->success('success',200,$data);
        }

        return $this->error('error');
    }

}
