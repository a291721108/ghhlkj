<?php

namespace App\Http\Controllers\Common;

use App\Exceptions\ErrorCode;
use App\Models\UserSend;
use App\Service\Common\RedisService;
use Illuminate\Http\Request;
use Mrgoon\AliSms\AliSms;


class SendMsgController extends BaseController
{
    /**
     * @catalog API/短信
     * @title 登录短信验证码
     * @description 登录短信验证吗
     * @method post
     * @url 47.92.82.25/api/sendSms
     *
     *
     * @param phone 必选 string 手机号
     *
     * @return {"code":200,"msg":"发送成功","data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param data array 返回数据
     *
     * @remark
     * @number 3
     */
    public function sendSms(Request $request)
    {
        $phone = $request->phone;

        $this->validate($request, [
            'phone' => 'required',
        ]);

        // 验证手机号格式
        if (!validatePhone($phone)) {
            return $this->error('phone_error');
        }

        $this->errorCode = new ErrorCode();
        $aliSms          = new AliSms();

        // 防止恶意验签
        if ( RedisService::get('phone_' . $phone)) {
            return $this->error('code_has_time');
        }

        $num = rand(100000, 999999);
        $res = $aliSms->sendSms($phone, 'SMS_154950909', ['code' => $num]);

        if ($res->Code == "OK") {
            // 把手机号码存入redis缓存 300秒有效期限
            RedisService::set('phone_' . $phone, $num, 300);

            // 记录到数据库中
            $data = [
                'code'          => $num,
                'send_time'     => time(),
                'type'          => 1,
                'phone'         => $phone,
                'created_at'    => time()
            ];

            UserSend::insert($data);

            return $this->success('code_send_success');
        }

        return $this->error('code_send_error');
    }
}
