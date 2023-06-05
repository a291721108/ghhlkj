<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Common\BaseController;
use App\Service\Front\OrderService;
use Illuminate\Http\Request;

class OrderController extends BaseController
{

    /**
     * @catalog app端/订单
     * @title 订单生成
     * @description 订单生成
     * @method post
     * @url 47.92.82.25/api/placeAnOrder
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param institution_id 必选 int 机构id
     * @param institution_type 必选 int 房间类型id
     * @param start_date 必选 string 开始时间
     * @param order_phone 必选 int 联系方式
     * @param payment_method 必选 int 支付方式
     * @param amount_paid 必选 int 已支付定金
     * @param order_remark 非必选 string 备注
     * @param contacts 必选 string 联系人
     * @param contacts_card 必选 string 联系人身份证
     * @param status 必选 int 状态1待付款,2已入住,3已完成,4已取消,5定金已支付,6预约
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function placeAnOrder(Request $request)
    {

        $this->validate($request, [
            'institution_id'        => 'required|numeric',
            'institution_type'      => 'required|numeric',
            'start_date'            => 'required',
            'payment_method'        => 'required|numeric',
            'amount_paid'           => 'required|numeric',
            'order_phone'           => 'required|numeric',
            'contacts'              => 'required',
            'contacts_card'         => 'required',
            'status'                => 'required|numeric'
        ]);

        $data = OrderService::placeAnOrder($request);

        if ($data) {
            return $this->success('success');
        }

        return $this->error('error');
    }

    /**
     * @catalog app端/订单
     * @title 个人订单列表
     * @description 个人订单列表
     * @method post
     * @url 47.92.82.25/api/orderList
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param page 必选 int 页
     * @param page_size 必选 int 数据
     * @param status 非必选 int 状态
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param total int 总条数
     * @return_param current_page int 当前页
     * @return_param page_size int 每页请求条数
     * @return_param id int id
     * @return_param user_id [] 用户信息
     * @return_param order_no string 订单编号
     * @return_param total_amount float 订单总金额
     * @return_param payment_method string 支付方式
     * @return_param institution_id string 机构名称
     * @return_param institution_type string 房间类型
     * @return_param discount_coupon string 优惠券（待定）
     * @return_param start_date string 入住开始时间
     * @return_param end_date string 入住结束时间
     * @return_param order_phone string 联系人手机号
     * @return_param order_remark string 备注
     * @return_param status string 状态
     * @return_param created_at string 创建时间
     *
     * @remark
     * @number 1
     */
    public function orderList(Request $request)
    {

        $this->validate($request, [
            'page'      => 'required|numeric',
            'page_size' => 'required|numeric',
        ]);

        $data = OrderService::orderList($request);

        if ($data) {
            return $this->success('success', '200', $data);
        }

        return $this->error('error');
    }

    /**
     * @catalog app端/订单
     * @title 通过id获取个人订单详情
     * @description 个人订单列表
     * @method post
     * @url 47.92.82.25/api/userReservationRecord
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param id 必选 int 订单id
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param total int 总条数
     * @return_param current_page int 当前页
     * @return_param page_size int 每页请求条数
     * @return_param id int id
     * @return_param user_id [] 用户信息
     * @return_param order_no string 订单编号
     * @return_param total_amount float 订单总金额
     * @return_param payment_method string 支付方式
     * @return_param institution_id string 机构名称
     * @return_param institution_type string 房间类型
     * @return_param discount_coupon string 优惠券（待定）
     * @return_param start_date string 入住开始时间
     * @return_param end_date string 入住结束时间
     * @return_param order_phone string 联系人手机号
     * @return_param order_remark string 备注
     * @return_param status string 状态
     * @return_param created_at string 创建时间
     *
     * @remark
     * @number 1
     */
    public function userReservationRecord(Request $request)
    {

        $this->validate($request, [
            'orderId'      => 'required|numeric',
        ]);

        $data = OrderService::userReservationRecord($request);

        if ($data) {
            return $this->success('success', '200', $data);
        }

        return $this->error('error');
    }

    /**
     * @catalog app端/订单
     * @title 取消预约
     * @description 取消预约
     * @method post
     * @url 47.92.82.25/api/cancelReservation
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param orderId 必选 int 订单id
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function cancelReservation(Request $request)
    {

        $this->validate($request, [
            'orderId'      => 'required|numeric',
        ]);

        $data = OrderService::cancelReservation($request);

        if ($data == 'success') {
            return $this->success('success');
        }

        return $this->error('error');
    }

    /**
     * @catalog app端/订单
     * @title 订单支付
     * @description 订单支付
     * @method post
     * @url 47.92.82.25/api/paymentOrder
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param orderId 必选 int 订单id
     * @param total_amount 必选 int 订单总金额
     * @param amount_paid 必选 int 已支付金额
     * @param wait_pay 必选 int 待支付金额
     * @param start_date 必选 int 开始日期
     * @param end_date 必选 int 结束日期
     * @param order_remark 非必选 string 备注
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function paymentOrder(Request $request)
    {

        $this->validate($request, [
            'orderId'      => 'required|numeric',
        ]);

        $data = OrderService::paymentOrder($request);

        if ($data == 'success') {
            return $this->success('success');
        }

        return $this->error('error');
    }

    /**
     * @catalog app端/订单
     * @title 申请退房
     * @description 申请退房
     * @method post
     * @url 47.92.82.25/api/checkOutApply
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param order_id 必选 int 订单id
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function checkOutApply(Request $request)
    {

        $this->validate($request, [
            'order_id'      => 'required|numeric',
        ]);

        $data = OrderService::checkOutApply($request);

        if ($data == 'success') {
            return $this->success('success');
        }

        return $this->error('error');
    }

    /**
     * @catalog app端/订单
     * @title 取消申请退房
     * @description 取消申请退房
     * @method post
     * @url 47.92.82.25/api/offCheckOutApply
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param id 必选 int 订单id
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function offCheckOutApply(Request $request)
    {

        $this->validate($request, [
            'id'      => 'required|numeric',
        ]);

        $data = OrderService::offCheckOutApply($request);

        if ($data == 'success') {
            return $this->success('success');
        }

        return $this->error('error');
    }

    /**
     * @catalog app端/订单
     * @title 申请续费
     * @description 申请续费
     * @method post
     * @url 47.92.82.25/api/applyRenewal
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param institution_id 必选 int 机构id
     * @param institution_type 必选 int 房间类型id
     * @param room_number 必选 int 房间号id
     * @param start_date 必选 int 开始时间
     * @param end_date 必选 int 结束时间
     * @param orderId 必选 int 订单id
     * @param phone 必选 int 手机号
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
    public function applyRenewal(Request $request)
    {

        $this->validate($request, [
            'institution_id'      => 'required|numeric',
            'institution_type'    => 'required|numeric',
            'room_number'         => 'required|numeric',
            'start_date'          => 'required',
            'end_date'            => 'required',
            'phone'               => 'required',
        ]);

        $data = OrderService::applyRenewal($request);

        if ($data) {
            return $this->success('success');
        }

        return $this->error('error');
    }
}
