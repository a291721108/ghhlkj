<?php

namespace App\Http\Controllers\Admin;

use App\Libraries\AliyunOcr;
use App\Service\Admin\AdminService;
use App\Service\Admin\OrderNotificationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Common\BaseController;
use AlibabaCloud\Client\AlibabaCloud;


class OrderNotificationController extends BaseController
{
    /**
     * @catalog 商家端/订单
     * @title 订单列表
     * @description 订单列表
     * @method post
     * @url 39.105.183.79/admin/getOrderList
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param page 必选 int 页
     * @param page_size 必选 int 数据
     * @param status 非必选 int 状态
     *
     * @return {"meta":{"status":200,"msg":"预约成功"},"data":[]}
     *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     * @return_param token string token
     *
     * @remark
     *
     * @number 2
     */
    public function getOrderList(Request $request)
    {
        $this->validate($request, [
            'page'      => 'required|numeric',
            'page_size' => 'required|numeric',
        ]);

        $data = OrderNotificationService::getOrderList($request);

        if ($data){
            return $this->success('success',200,$data);

        }
        return $this->error('error');

    }

    /**
     * @catalog 商家端/订单
     * @title 同意入住
     * @description 同意入住
     * @method post
     * @url 39.105.183.79/admin/noDepositAgreed
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param bookingId 必选 int 无押金预约id
     * @param roomID 必选 int 房间号id
     *
     * @return {"meta":{"status":200,"msg":"预约成功"},"data":[]}
     *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     * @return_param token string token
     *
     * @remark
     * @number 2
     */
    public function noDepositAgreed(Request $request)
    {
        $this->validate($request, [
            'bookingId'      => 'required|numeric',
            'roomID'        => 'required|numeric'
        ]);

        $data = OrderNotificationService::noDepositAgreed($request);

        if ($data){
            return $this->success($data);

        }
        return $this->error('error');

    }

    /**
     * @catalog 商家端/订单
     * @title 同意退款
     * @description 同意退款
     * @method post
     * @url 39.105.183.79/admin/agreeRefund
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param refundId 必选 int 订单id
     * @param amount 必选 int 退款金额
     *
     * @return {"meta":{"status":200,"msg":"预约成功"},"data":[]}
     *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     * @return_param token string token
     *
     * @remark
     * @number 2
     */
    public function agreeRefund(Request $request)
    {
        $this->validate($request, [
            'refundId'     => 'required|numeric',
            'amount'       => 'required|numeric',
        ]);

        $data = OrderNotificationService::agreeRefund($request);

        if ($data){
            return $this->success($data);
        }
        return $this->error('error');

    }

    /**
     * @catalog 商家端/订单
     * @title 拒绝退款
     * @description 拒绝退款
     * @method post
     * @url 39.105.183.79/admin/refusalRefund
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param refundId 必选 int 订单id
     *
     * @return {"meta":{"status":200,"msg":"预约成功"},"data":[]}
     *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     * @return_param token string token
     *
     * @remark
     * @number 2
     */
    public function refusalRefund(Request $request)
    {
        $this->validate($request, [
            'refundId'     => 'required|numeric',
//            'amount'       => 'required|numeric',
        ]);

        $data = OrderNotificationService::refusalRefund($request);

        if (is_bool($data)){
            return $this->success('success');
        }
        return $this->error('error');

    }

    /**
     * @catalog 商家端/订单
     * @title 同意续费
     * @description 同意续费
     * @method post
     * @url 39.105.183.79/admin/agreeRenew
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param renewalId 必选 int 续费申请id
     *
     * @return {"meta":{"status":200,"msg":"预约成功"},"data":[]}
     *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     * @return_param token string token
     *
     * @remark
     * @number 2
     */
    public function agreeRenew(Request $request)
    {
        $this->validate($request, [
            'renewalId'     => 'required|numeric',
        ]);

        $data = OrderNotificationService::agreeRenew($request);

        if ($data){
            return $this->success($data);

        }
        return $this->error('error');

    }

    /**
     * @catalog 商家端/订单
     * @title 订单详情
     * @description 订单详情
     * @method post
     * @url 39.105.183.79/admin/getOrderDetail
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param orderId 必选 int 订单id
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":{"id":1,"user_id":2,"order_no":"GH20230524100700001","total_amount":"0.00","amount_paid":"0.00","wait_pay":"0.00","institution_id":1,"institution_type":1,"roomNum":"","visitDate":"2023-05-27","start_date":"","end_date":"","order_phone":"15135345970","order_remark":"","refundNot":0,"renewalNot":0,"contacts":"薛丁丁","contacts_card":"142625199911044818","status":4,"created_at":"2023-05-24 10:07:52"}}
     *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     * @return_param token string token
     *
     * @remark
     * @number 2
     */
    public function getOrderDetail(Request $request)
    {
        $this->validate($request, [
            'orderId'     => 'required|numeric',
        ]);

        $data = OrderNotificationService::getOrderDetail($request);

        if (is_array($data)){
            return $this->success('success',200,$data);

        }
        return $this->error('error');
    }
}
