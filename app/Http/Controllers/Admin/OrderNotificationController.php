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
     * @url 39.105.183.79/admin/getAdminInfo
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2FkbWluXC9sb2dpbiIsImlhdCI6MTY0ODExMzM3NSwiZXhwIjoxNjQ4MTE2OTc1LCJuYmYiOjE2NDgxMTMzNzUsImp0aSI6IkR2dDVLNmtTdDZ5V0NhdDMiLCJzdWIiOjgsInBydiI6ImFjYmI0NTAwY2UzMTc3YjA5ZWZiMzNiMTFlMzIxY2NkMmIzM2M3YWMifQ.mzzLjIsgnOB1kLb1RhirL3hmKVI636BtmoGVrT-Uoes","admin_name":"张三","admin_phone":"17865992641","company_id":1,"created_at":1648113375}}
     * *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     * @return_param token string token
     * @return_param admin_name string 姓名
     * @return_param admin_phone string 手机号
     * @return_param company_id string 公司ID
     *
     * @remark
     * @number 2
     */
    public function subscribeCheck(Request $request)
    {
        $this->validate($request, [
            'bookingId'      => 'required|numeric',
        ]);

        $data = OrderNotificationService::subscribeCheck($request);

        if ($data) {
            return $this->success('success', '200', $data);
        }

        return $this->error('error');
    }

}
