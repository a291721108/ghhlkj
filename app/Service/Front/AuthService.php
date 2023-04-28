<?php

namespace App\Service\Front;

use App\Events\FrontLoginEvent;
use App\Models\User;
use App\Models\UserExt;
use App\Models\UserSend;
use App\Models\UserWxInfo;
use App\Service\Common\FunService;
use App\Service\Common\RedisService;
use Illuminate\Support\Facades\Auth;
use StdClass;
use function Symfony\Component\String\u;

class AuthService
{

    /**
     * 用户登录逻辑
     * @param $request
     */
    public static function login($request)
    {
        $phone = $request->phone;
        $password = $request->password;

        // 判断用户是否存在
        $useInfo = User::where('phone', '=', $phone)->where('status', '=', User::USER_STATUS_ONE)->first();
        if (!$useInfo) {
            return 'The user does not exist.';
        }

        // 判断用户秘密是否正确
        $password = md5($password) . $useInfo->salt;
        if ($password !== $useInfo->password . $useInfo->salt) {
            return 'password_error';
        }

        //  登录成功 为用户颁发token
        $token = Auth::guard('api')->login($useInfo);

        // 将token存在redis中 过期时间设置为1天
        $key = "gh_user_front_token_" . $useInfo->id;
        RedisService::set($key, $token);

        // redis存入异常抛错
        if (!RedisService::get($key)) {
            return 'redis_write_token_error';
        }

        // 时间监听 处理登录完成之后的逻辑
        $obj = new StdClass();
        $obj->id = $useInfo->id;
        event(new FrontLoginEvent($obj));

        return self::loginReturn($token,$useInfo);

    }

    /**
     * 密码修改
     * @param $request
     */
    public static function register($request)
    {

        $phone = $request->phone;
        $password = $request->password;

        // 判断用户是否存在
        $useInfo                = User::where('phone', '=', $phone)->first();
        $useInfo->password      = md5($password);
        $useInfo->salt          = rand(1, 100);
        $useInfo->updated_at    = time();
//        $useInfo->name =$request->name;
//        $useInfo->img = $request->img;
//        $useInfo->email = $request->email;

        return $useInfo->save();

    }

    /**
     * 基本信息修改
     * @param $request
     */
    public static function getInfo()
    {
        $userInfo = Auth::user();

        return [
            'name'      => $userInfo->name,
            'img'       => $userInfo->img,
            'gender'    => User::GENDER_MSG_ARRAY[$userInfo->gender],
            'birthday'  => ytdTampTime($userInfo->birthday),
        ];

    }

    /**
     * 基本信息修改
     * @param $request
     */
    public static function upInfo($request)
    {
        $userInfo = Auth::user();

        // 判断用户是否存在
        $useInfo                = User::where('id', '=', $userInfo->id)->first();

        $useInfo->name          = $request->name;
        $useInfo->img           = $request->img;
        $useInfo->gender        = $request->email;
        $useInfo->birthday      = $request->birthday;
        $useInfo->updated_at    = time();

        if ($useInfo->save()){
            return 'success';
        }
        return 'error';
    }

    /**
     * 修改手机号
     * @param $request
     */
    public static function upTel($request)
    {
        $userInfo = Auth::user();

        $phone    = $request->tel;
        $code     = $request->code;

        // 判断是否有验证吗
        $sendInfo = UserSend::where('phone', '=', $phone)->orderBy('id', 'desc')->first();
//        dd($sendInfo);
        if (!$sendInfo) {
            return 'phone_error';
        }

        //  验证吗是否过期 有效期限五分钟
        if (time() >= ($sendInfo->send_time + 300)) {
            return 'code_expired';
        }

        // 验证码错误
        if ($sendInfo->code !== intval($code)) {
            return 'code_error';
        }
        // 判断用户是否存在
        $useInfo                = User::where('id', '=', $userInfo->id)->first();

        $useInfo->phone         = $request->tel;
        $useInfo->updated_at    = time();

        if ($useInfo->save()){
            return 'success';
        }
        return 'error';
    }

    /**
     * 忘记密码
     * @return string|bool
     */
    public static function forgotPassword($request)
    {
        $code       = $request->dxcodess;
        $phone      = $request->phone;
        $passwords  = $request->passwords;

        // 判断是否有验证吗
        $sendInfo = UserSend::where('phone', '=', $phone)->orderBy('id', 'desc')->first();

        if (!$sendInfo) {
            return 'phone_error';
        }

        //  验证吗是否过期 有效期限五分钟
        if (time() >= ($sendInfo->send_time + 300)) {
            return 'code_expired';
        }

        // 验证码错误
        if ($sendInfo->code !== intval($code)) {
            return 'code_error';
        }

        $useInfo = User::where('phone', '=', $phone)->first();
        $useInfo->password = md5($passwords);
        $useInfo->salt = rand(1, 100);
        RedisService::del('gh_user_front_token_' . $useInfo->id);

        if ($useInfo->save()) {
            return 'success';
        }

        return 'error';
    }

    /**
     * 验证码登录
     * @return array|string
     */
    public static function sendSmsLogin($request)
    {
        $code = $request->dxcodess;
        $phone = $request->phone;

        // 判断是否有验证吗
        $sendInfo = UserSend::where('phone', '=', $phone)->orderBy('id', 'desc')->first();

//        if (!$sendInfo) {
//            return 'phone_error';
//        }
//
//        //  验证吗是否过期 有效期限五分钟
//        if (time() >= ($sendInfo->send_time + 300)) {
//            return 'code_expired';
//        }
//
//        // 验证码错误
//        if ($sendInfo->code !== intval($code)) {
//            return 'code_error';
//        }

        // 判断用户是否存在
        $useInfo = User::where('phone', '=', $phone)->where('status', '=', User::USER_STATUS_ONE)->first();

        if (!$useInfo) {
            //如果没有这个手机号插入数据库
            $data = [
                'name'          => FunService::userNumber(),
                'phone'         => $phone,
                'img'           => env('APP_URL') . env('qrcode_dir') . '/my_img_default.png',
                'status'        => User::USER_STATUS_ONE,
                'created_at'    => time()
            ];

            $ins = User::insertGetId($data);
            $dataExt = [
                'user_id'       => $ins,
                'status'        => UserExt::USER_STATUS_ONE,
                'result'        => UserExt::USER_RESULT_ONE,
                'created_at'    => time(),
            ];

            $userExtSave = UserExt::insert($dataExt);
            if (!$userExtSave){
                return 'error';
            }
            $dataWx = [
                'user_id'   => $ins,
                'created_at'    => time(),
            ];

            $UserWxInfoSave = UserWxInfo::insert($dataWx);
            if (!$UserWxInfoSave){
                return 'error';
            }

            return self::loginReturn($token = '',$useInfo);
        }

        //  登录成功 为用户颁发token
        $token = Auth::guard('api')->login($useInfo);

        // 将token存在redis中 过期时间设置为1天
        $key = "gh_user_front_token_" . $useInfo->id;
        RedisService::set($key, $token);

        return self::loginReturn($token,$useInfo);
    }

    public static function loginReturn($token,$useInfo){

        return [
            'api_token'             => $token ?? '',
            'user_id'               => $useInfo->id ?? '',
            'user_username'         => $useInfo->name?? '',
            'password'              => $useInfo->password?? '',
            'user_img'              => $useInfo->img?? '',
            'user_email'            => $useInfo->email ?? '',
            'user_address'          => $useInfo->address ?? '',
            'user_phone'            => $useInfo->phone?? '',
            'pay_password'          => $useInfo->pay_password?? '',
            'qr_code'               => $useInfo->qr_code?? '',
            'user_gender'           => User::GENDER_MSG_ARRAY[$useInfo->gender] ?? '',
            'user_birthday'         => ytdTampTime($useInfo->birthday) ?? '',
            'wx_status'             => UserWxInfo::getIdByWxInfo($useInfo->id) ?? '',
            'data'                  => UserExt::getMsgByUserId($useInfo->id) ?? '',
        ];
    }

    /**
     * 注销账户
     * @return string|bool
     */
    public static function closeAnAccount()
    {
        $userInfo = User::getUserInfo();
        $userInfo->status = User::USER_STATUS_TWO;
        $userInfo->save();

        if ($userInfo) {
            return 'success';
        }
        return 'error';
    }

    /**
     * 安全退出
     * @return string|bool
     */
    public static function safeWithdrawing()
    {
        $user       = User::getUserInfo();
        $userInfo   = User::logout();
        // 将token存在redis中 过期时间设置为1天
        RedisService::del('gh_user_front_token_' . $user->id);
        if (!$userInfo) {
            return "safe withdrawing";
        }
        return "error";
    }

    /**
     * 身份证正面
     * @return string|bool
     */
    public static function fontPhotoCard($request)
    {
        $userInfo       = User::getUserInfo();
        $id_front_photo = $request->id_front_photo;
        $id_back_photo  = $request->id_back_photo;

        $userExt = UserExt::where('user_id', '=', $userInfo->id)->first();
        $userExt->id_front_photo    = $id_front_photo;
        $userExt->id_back_photo     = $id_back_photo;
        $userExt->updated_at        = strtotime(time());
        $userExt->save();

        if (!$userExt) {
            return "error";
        }
        return 'success';
    }

    /**
     * 身份证反面
     * @return string|bool
     */
    public static function backPhotoCard($request)
    {
        $userInfo       = User::getUserInfo();
        $id_back_photo  = $request->id_back_photo;

        $userExt = UserExt::where('user_id', '=', $userInfo->id)->first();
        $userExt->id_back_photo = $id_back_photo;
        $userExt->updated_at    = strtotime(time());
        $userExt->save();
        if (!$userExt) {
            return "error";
        }

        return 'success';
    }

    /**
     * 身份证正面识别
     * @return string|bool
     */
    public static function positiveRecognition($request)
    {
        $id_front_photo = $request->id_front_photo;

        $host = "https://fenxiao.market.alicloudapi.com";
        $path = "/thirdnode/ImageAI/idcardfrontrecongnition";
        $method = "POST";
        $appcode = "b5dfda1e87e4433eba16d8e0b20179d1";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
        $querys = "";
//        $bodys = "base64Str=http://47.92.82.25:8080//upload//front//20230407170451_2e2c2678918e1d578bdcc68f11d5d45.jpg";
        $bodys = "base64Str=" . $id_front_photo;
        $url = $host . $path;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        $res = curl_exec($curl);
        $obj = json_decode($res);
        $jsonStr = json_encode($obj);

        // todo 身份证识别  待完善

//        $new_json = str_replace(
//            array('error_code', 'reason', 'result'),
//            array('status', 'msg', 'data'),
//            $jsonStr
//        );

        return $res;
    }

    /**
     * 身份证反面识别
     * @return string|bool
     */
    public static function negativeRecognition($request)
    {
        $id_back_photo = $request->id_back_photo;

        $host = "https://fenxiao.market.alicloudapi.com";
        $path = "/thirdnode/ImageAI/idcardbackrecongnition";
        $method = "POST";
        $appcode = "b5dfda1e87e4433eba16d8e0b20179d1";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
        $querys = "";
//        $bodys = "base64Str=http://47.92.82.25:8080/upload/front/20230411100426_b488dcb8fd4cc24737d79c823d5e3b2.jpg";
        $bodys = "base64Str=" . $id_back_photo;

        $url = $host . $path;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);

        $res = curl_exec($curl);
        $obj = json_decode($res);
        $jsonStr = json_encode($obj);

        // todo 身份证识别  待完善

//        if ($obj->error_code == 0) {
//            $new_json = str_replace(
//                array('error_code', 'reason', 'result'),
//                array('status', 'msg', 'data'),
//                $jsonStr
//            );
//            return $new_json;
//        }
//
        return $res;
    }

    /**
     * 身份录入
     * @return string|bool
     */
    public static function authenticationEntry($request)
    {
        $userInfo       = User::getUserInfo();
        $userExt = UserExt::where('user_id',$userInfo->id)->first();

        $userExt->id_number         = $request->id_number;
        $userExt->id_type           = $request->id_type;
        $userExt->id_name           = $request->id_name;
        $userExt->id_province       = $request->id_province;
        $userExt->id_city           = $request->id_city;
        $userExt->id_starttime      = strtotime($request->id_starttime);
        $userExt->id_endtime        = strtotime($request->id_endtime);
        $userExt->status            = $request->status;
        $userExt->authenticate_time = strtotime($request->authenticate_time);
        $userExt->result            = $request->result;
        $userExt->updated_at        = strtotime(time());

        if ($userExt->save()) {
            return 'success';
        }

        return 'error';
    }

    /**
     * 设置支付密码
     * @return string|bool
     */
    public static function setPayPassword($request)
    {
        $userInfo       = User::getUserInfo();

        // 加密密码
        $password = $request->pay_password;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 验证密码
//        $isMatch = password_verify($password, $userInfo->pay_password);

        $user = User::where('id',$userInfo->id)->first();
        $user->pay_password = $hashedPassword;

        if ($user->save()) {
            return 'success';
        }

        return 'error';
    }

    /**
     * 验证支付密码
     * @return string|bool
     */
    public static function upPayPassword($request)
    {
        $userInfo       = User::getUserInfo();
        // 加密密码
        $password = $request->pay_password;

        // 验证密码
        $isMatch = password_verify($password, $userInfo->pay_password);
        if ($isMatch){

            return 'success';
        }

        return 'error';
    }

    /**
     * 验证身份证号
     * @return array|string
     */
    public static function validateCard($request)
    {
        $userInfo       = User::getUserInfo();
        $user = UserExt::where('user_id',$userInfo->id)->first();

        if (getCheckCode($request->id_card) != getCheckCode($user->id_number)){
            return "card_verification_failure";
        }

        return 'success';
    }

    /**
     * 手机号验证
     * @return array|string
     */
    public static function validateTel($request)
    {
        $userInfo   = User::getUserInfo();
        $phone      = $userInfo->phone;
        $code       = $request->dxcodess;

        // 判断是否有验证吗
        $sendInfo = UserSend::where('phone', '=', $phone)->orderBy('id', 'desc')->first();

        if (!$sendInfo) {
            return 'phone_error';
        }

        //  验证吗是否过期 有效期限五分钟
        if (time() >= ($sendInfo->send_time + 300)) {
            return 'code_expired';
        }

        // 验证码错误
        if ($sendInfo->code !== intval($code)) {
            return 'code_error';
        }
        return 'success';
    }
}
