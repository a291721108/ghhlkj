<?php

namespace App\Http\Controllers\Common;

use App\Exceptions\ErrorCode;
use App\Models\User;
use App\Models\UserSend;
use App\Service\Common\RedisService;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mrgoon\AliSms\AliSms;


class PersonalCodeController extends BaseController
{

    /**
     * 初始化
     */
    public function __construct()
    {
        $userInfo = Auth::user();
        $this->id = $userInfo->id;
        $this->name = $userInfo->name;
        $this->img = $userInfo->img;
        $this->qr_code = $userInfo->qr_code;
        $this->phone = $userInfo->phone;
    }

    /**
     * @catalog API/公共
     * @title 个人二维码
     * @description 个人二维码
     * @method post
     * @url 47.92.82.25/api/qrCode
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":{"qr_code":"http:\/\/47.92.82.25\/.\/upload\/qrcode\/GH_qr_code15135345970.jpg"}}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param data array 返回数据
     * @return_param url string 图片路径
     *
     * @remark
     * @number 6
     */
    public function qrCode(Request $request)
    {
        // 保存位置
        $dir = env("QRCODE_DIR");
        $path = $dir . '/GH_qr_code' . $this->phone . '.jpg';

        // Create QR code
        $res = [
            'id' => $this->id,
            'name'  => $this->name,
            'phone' => $this->phone,
            'img'   => $this->img
        ];
        $qrCode = QrCode::create(json_encode($res))

            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Create generic logo
        $logo = Logo::create($dir . '/img_logo.png')
            ->setResizeToWidth(50);

        $writer = new PngWriter();
        $result = $writer->write($qrCode, $logo);

        // todo 二维码数据验证 待完善
        // ----------------------
        $user = User::where('id', $this->id)->first();
        $user->qr_code = env('APP_URL') . $path;

        if (!$user->save()) {
            return $this->error("error");
        }

//        header('Content-Type: ' . $result->getMimeType());
//        echo $result->getString();

        // Save it to a file
        $result->saveToFile($path);

        $data = [
            'qr_code'   => env("APP_URL") . $path,
        ];

        if (is_file($path)) {
            return $this->success('success',200,$data);
        }

        $this->error('error');

    }
}
