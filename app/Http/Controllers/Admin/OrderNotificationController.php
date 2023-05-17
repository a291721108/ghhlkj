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
        return 'error';
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
        return 'error';
    }

    /***
     * showdoc
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
        return 'error';
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
        return 'error';
    }

}
