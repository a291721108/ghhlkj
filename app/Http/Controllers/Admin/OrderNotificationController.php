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

        if ($data == 'successful_refund'){
            return $this->success('successful_refund');
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

        if ($data == "book_successfully"){
            return $this->success('book_successfully');

        }
        return $this->error($data);

    }

    /**
     * @catalog 商家端/订单
     * @title 续费详情
     * @description 续费详情
     * @method post
     * @url 39.105.183.79/admin/agreeRenewDetail
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param orderId 必选 int 订单id
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
    public function agreeRenewDetail(Request $request)
    {
        $this->validate($request, [
            'orderId'     => 'required|numeric',
        ]);

        $data = OrderNotificationService::agreeRenewDetail($request);

        if (is_array($data)){
            return $this->success('success',200,$data);

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
     * @return_param id int id
     * @return_param user_id string 用户id
     * @return_param order_no string 订单编号
     * @return_param total_amount float 订单总金额
     * @return_param amount_paid float 已支付金额
     * @return_param wait_pay float 待支付金额
     * @return_param institution_id int 机构id
     * @return_param institution_type 【】 房间类型
     * @return_param roomNum int 房间号
     * @return_param visitDate string 看房日期
     * @return_param start_date string 开始日期
     * @return_param end_date string 结束日期
     * @return_param order_phone string 手机号
     * @return_param order_remark string 备注
     * @return_param refundNot string 是否退款（1是   0否）
     * @return_param renewalNot string 是否续费（1是   0否）
     * @return_param contacts string 联系人
     * @return_param contacts_card string 联系人身份证
     * @return_param status string 订单状态（1待付款  2已入住  3已完成 4已预约 0取消）
     *
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
