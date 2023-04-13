<?php

namespace App\Service\Front;

use App\Events\FrontLoginEvent;
use App\Models\User;
use App\Models\UserExt;
use App\Models\UserSend;
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

        return [
            'api_token'             => $token,
            'user_id'               => $useInfo->id,
            'user_username'         => $useInfo->name,
            'password'              => $useInfo->password,
            'user_img'              => $useInfo->img,
            'user_email'            => $useInfo->email,
            'user_address'          => $useInfo->address,
            'user_phone'            => $useInfo->phone,
            'user_gender'           => User::GENDER_MSG_ARRAY[$useInfo->gender] ?? '',
            'user_birthday'         => ytdTampTime($useInfo->birthday) ?? '',
            'data'                  => UserExt::getMsgByUserId($useInfo->id),
        ];

    }

    /**
     * 用户信息修改
     * @param $request
     */
    public static function register($request)
    {

        $phone = $request->phone;
        $password = $request->password;

        // 判断用户是否存在
        $useInfo = User::where('phone', '=', $phone)->first();
        $useInfo->password = md5($password);
        $useInfo->salt = rand(1, 100);

//        $useInfo->name =$request->name;
//        $useInfo->img = $request->img;
//        $useInfo->email = $request->email;
        $useInfo->updated_at = time();

        return $useInfo->save();

    }

    /**
     * 忘记密码
     * @return string|bool
     */
    public static function forgotPassword($request)
    {
        $code = $request->dxcodess;
        $phone = $request->phone;
        $passwords = $request->passwords;

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
        $useInfo->user_password = md5($passwords);
        $useInfo->salt = rand(1, 100);


        if ($useInfo->save()) {
            return 'success';
        }

        return 'error';
    }

    /**
     * 验证码登录
     * @return array
     */
    public static function sendSmsLogin($request)
    {
        $code = $request->dxcodess;
        $phone = $request->phone;

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

        // 判断用户是否存在
        $useInfo = User::where('phone', '=', $phone)->where('status', '=', User::USER_STATUS_ONE)->first();

        if (!$useInfo) {
            //如果没有这个手机号插入数据库
            $data = [
                'name' => "游客111",
                'phone' => $phone,
                'status' => User::USER_STATUS_ONE,
                'created_at' => time()
            ];

            $ins = User::insertGetId($data);
            $dataExt = [
                'user_id' => $ins,
                'status' => UserExt::USER_STATUS_ONE,
                'result' => UserExt::USER_RESULT_ONE,
                'created_at' => time(),
            ];

            UserExt::insert($dataExt);

            return [
                'user_id'               => $useInfo->id,
                'user_username'         => $useInfo->name,
                'password'              => $useInfo->password,
                'user_img'              => $useInfo->img ?? '',
                'user_email'            => $useInfo->email ?? '',
                'user_address'          => $useInfo->address ?? '',
                'user_phone'            => $useInfo->phone ?? '',
                'user_gender'           => User::GENDER_MSG_ARRAY[$useInfo->gender] ?? '',
                'user_birthday'         => ytdTampTime($useInfo->birthday) ?? '',
                'data'                  => UserExt::getMsgByUserId($useInfo->id)
            ];
        }

        //  登录成功 为用户颁发token
        $token = Auth::guard('api')->login($useInfo);

        // 将token存在redis中 过期时间设置为1天
        $key = "gh_user_front_token_" . $useInfo->id;
        RedisService::set($key, $token);

        return [
            'api_token' => $token,
            'user_id' => $useInfo->id,
            'user_username' => $useInfo->name,
            'password'              => $useInfo->password,
            'user_img' => $useInfo->img,
            'user_email' => $useInfo->email ?? '',
            'user_address' => $useInfo->address ?? '',
            'user_phone' => $useInfo->phone,
            'user_gender' => User::GENDER_MSG_ARRAY[$useInfo->gender] ?? '',
            'user_birthday' => ytdTampTime($useInfo->birthday) ?? '',
            'data' => UserExt::getMsgByUserId($useInfo->id)
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
        $user = User::getUserInfo();
        $userInfo = User::logout();
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
        $userInfo = User::getUserInfo();
        $id_front_photo = $request->id_front_photo;
        $id_back_photo = $request->id_back_photo;

        $userExt = UserExt::where('user_id', '=', $userInfo->id)->first();
        $userExt->id_front_photo = $id_front_photo;
        $userExt->id_back_photo = $id_back_photo;
        $userExt->updated_at = strtotime(time());
        $userExt->save();

        if (!$userExt) {
            return "error";
        }


    }

    /**
     * 身份证反面
     * @return string|bool
     */
    public static function backPhotoCard($request)
    {
        $userInfo = User::getUserInfo();
        $id_back_photo = $request->id_back_photo;

        $userExt = UserExt::where('user_id', '=', $userInfo->id)->first();
        $userExt->id_back_photo = $id_back_photo;
        $userExt->updated_at = strtotime(time());
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

        $new_json = str_replace(
            array('error_code', 'reason', 'result'),
            array('status', 'msg', 'data'),
            $jsonStr
        );
        if ($obj->error_code == 0) {

            return $new_json;
        }

        return 'error';
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

        if ($obj->error_code == 0) {
            $jsonStr = json_encode($obj);

            $new_json = str_replace(
                array('error_code', 'reason', 'result'),
                array('status', 'msg', 'data'),
                $jsonStr
            );
            return $new_json;
        }

        return 'error';
    }

}
