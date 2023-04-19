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

    public function qrCode(Request $request)
    {
        $userInfo = Auth::user();
        $pattern = '/^1[3-9]\d{9}$/'; // 手机号的正则表达式

        $url = 'https://www.baidu.com/';
        $qrCode = new QrCode($url);
        // Create QR code
        $qrCode->create($userInfo->id . $userInfo->name . $userInfo->phone . $userInfo->img)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Create generic logo
        $logo = Logo::create(env('QRCODE_DIR') . '/img_logo.png')
            ->setResizeToWidth(50);

        $writer = new PngWriter();
        $result = $writer->write($qrCode, $logo);

        // todo 二维码数据验证 待完善

        $user = User::find($userInfo->id)->first();
        $user->qr_code = env('APP_URL') . env('QRCODE_DIR') . '/gh' . $request->id . '.jpg';

        if (!$user->save()) {
            return $this->error("error");
        }

        header('Content-Type: ' . $result->getMimeType());
//        echo $result->getString();

        // Save it to a file
        $result->saveToFile(env('QRCODE_DIR') . '/gh' . $request->id . '.jpg');

        $data = $userInfo->id . ',' . $userInfo->name . ',' . $userInfo->phone . ',' . $userInfo->img;
        $result = explode(",", $data);
        $keys = array('id', 'name', 'phone', 'img'); // 自定义下标数组
        $newArr = array_combine($keys, $result);

        return $this->success('success', 200, $newArr);

    }
}
