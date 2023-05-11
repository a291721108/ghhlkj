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

    /***
     * showdoc
     * @catalog 商家端/订单
     * @title 同意入住(无定金)
     * @description 同意入住(无定金)
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

    /***
     * showdoc
     * @catalog 商家端/订单
     * @title 同意入住(已付定金)
     * @description 同意入住(已付定金)
     * @method post
     * @url 39.105.183.79/admin/depositAgreed
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param bookingRoomId 必选 int 无押金预约id
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
    public function depositAgreed(Request $request)
    {
        $this->validate($request, [
            'bookingRoomId'     => 'required|numeric',
            'roomID'            => 'required|numeric',
        ]);

        $data = OrderNotificationService::depositAgreed($request);

        if ($data){
            return $this->success($data);

        }
        return 'error';
    }

}
