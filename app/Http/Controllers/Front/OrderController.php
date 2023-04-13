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
     * @param start_date 必选 string 看房日期
     * @param end_date 必选 string 联系人
     * @param order_phone 必选 int 联系方式
     * @param payment_method 必选 int 联系方式
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
    public function placeAnOrder(Request $request)
    {

        $this->validate($request, [
            'institution_id'        => 'required|numeric',
            'institution_type'      => 'required|numeric',
            'start_date'            => 'required|numeric',
            'end_date'              => 'required|numeric',
            'order_phone'           => 'required|numeric',
            'payment_method'        => 'required|numeric',
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

}
