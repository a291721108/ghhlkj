<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Common\BaseController;
use App\Service\Front\AuthService;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    /**
     * @catalog app端/用户相关
     * @title 用户登录
     * @description 用户登录的接口
     * @method post
     * @url 47.92.82.25/api/login
     *
     * @param phone 必选 string 手机号(17821211068)
     * @param password 必选 string 用户密码(6-12数字加字母组成)(admin)
     *
     * @return {"meta":{"status":200,"msg":"登录成功"},"data":{"api_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80Ny45Mi44Mi4yNVwvYXBpXC9sb2dpbiIsImlhdCI6MTY4MDE0ODU1NiwiZXhwIjoxNjgxNDQ0NTU2LCJuYmYiOjE2ODAxNDg1NTYsImp0aSI6ImNoVXhHcHBpT2xDYlN6OXciLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.DC_a1lX4rxy4h3o5ufE6QPqPRod4nYENBtMDELyWTts","user_username":"1"}}
     *
     * @return_param api_token string api_token
     * @return_param name string 姓名
     *
     * @remark
     * @number 1
     */
    public function login(Request $request)
    {
        // 数据校验
        $this->validate($request, [
            'phone' => 'required|numeric',
            'password' => 'required'
        ]);

        // 验证手机号格式
        if (!validatePhone($request->phone)) {
            return $this->error('phone_error');
        }

        // 密码格式 6-12位 字符加数字组合
        if (strlen($request->password) < 6 || strlen($request->password) > 12) {
            return $this->error('password_length_error');
        }

        // 验证密码是否由字符和数字组成
        if (!validatePassword($request->password)) {
            return $this->error('password_style_error');
        }

        $res = AuthService::login($request);

        if (is_array($res)) {
            // 登入成功
            return $this->success('login_success', 200, $res);
        }

        return $this->error($res);
    }

    /**
     * @catalog app端/用户相关
     * @title 密码修改
     * @description 密码修改
     * @method post
     * @url 47.92.82.25/api/register
     *
     * @param phone 必选 int id
     * @param password 必选 string 用户密码(6-12数字加字母组成)
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param
     *
     * @remark
     * @number 1
     */
    public function register(Request $request)
    {
        // 数据校验
        $this->validate($request, [
            'phone' => 'required|numeric',
            'password' => 'required',
        ]);

        // 验证手机号格式
        if (!validatePhone($request->phone)) {
            return $this->error('phone_error');
        }

        // 密码格式 6-12位 字符加数字组合
        if (strlen($request->password) < 6 || strlen($request->password) > 12) {
            return $this->error('password_length_error');
        }

        // 验证密码是否由字符和数字组成
        if (!validatePassword($request->password)) {
            return $this->error('password_style_error');
        }

        $res = AuthService::register($request);

        if (is_bool($res)) {
            return $this->success('success');
        }

        return $this->error($res);
    }

    /**
     * @catalog app端/用户相关
     * @title 获取用户基本信息
     * @description 获取用户基本信息
     * @method post
     * @url 47.92.82.25/api/getInfo
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param
     *
     * @remark
     * @number 1
     */
    public function getInfo()
    {
        $res = AuthService::getInfo();

        return $this->success('success', 200, $res);
    }

    /**
     * @catalog app端/用户相关
     * @title 基本信息修改
     * @description 基本信息修改
     * @method post
     * @url 47.92.82.25/api/upInfo
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param name 必选 int 姓名
     * @param img 必选 string 头像
     * @param gender 必选 int 性别
     * @param birthday 必选 string 生日
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param
     *
     * @remark
     * @number 1
     */
    public function upInfo(Request $request)
    {
        // 数据校验
        $this->validate($request, [
            'name'      => 'required',
            'img'       => 'required',
            'gender'    => 'required',
            'birthday'  => 'required',
        ]);

        $res = AuthService::upInfo($request);

        if ($res == 'success') {
            return $this->success('success');
        }

        return $this->error($res);
    }

    /**
     * @catalog app端/用户相关
     * @title 手机号修改
     * @description 手机号修改
     * @method post
     * @url 47.92.82.25/api/upTel
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param tel 必选 int 手机号
     * @param code 必选 string 验证码
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param
     *
     * @remark
     * @number 1
     */
    public function upTel(Request $request)
    {
        // 数据校验
        $this->validate($request, [
            'tel'      => 'required|numeric',
            'code'     => 'required|numeric',
        ]);

        $res = AuthService::upTel($request);

        if ($res == 'success') {
            return $this->success('success');
        }

        return $this->error($res);
    }

    /**
     * @catalog app端/用户相关
     * @title 忘记密码
     * @description 忘记密码接口
     * @method post
     * @url 47.92.82.25/api/forgotPassword
     *
     * @param dxcodess 必选 int 验证吗
     * @param phone 必选 string 手机号
     * @param passwords 必选 string 密码
     *
     * @return {"code":200,"msg":"修改密码成功","data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param data array 返回数据
     *
     * @remark
     * @number 2
     */
    public function forgotPassword(Request $request)
    {

        $this->validate($request, [
            'dxcodess' => 'required',
            'phone' => 'required',
            'passwords' => 'required'
        ]);

        // 验证手机号格式
        if (!validatePhone($request->phone)) {
            return $this->error('phone_error');
        }

        // 密码格式 6-12位 字符加数字组合
        if (strlen($request->passwords) < 6 || strlen($request->passwords) > 12) {
            return $this->error('password_length_error');
        }

        $res = AuthService::forgotPassword($request);

        if ($res == 'success') {
            return $this->success('update_true');
        }

        return $this->error($res);
    }

    /**
     * @catalog app端/用户相关
     * @title 验证码登录
     * @description 用户验证码登录
     * @method post
     * @url 47.92.82.25/api/sendSmsLogin
     *
     * @param phone 必选 string 手机号
     * @param dxcodess 必选 int 验证码(6位数字加字母组成)
     *
     * @return {"meta":{"status":200,"msg":"登录成功"},"data":{"api_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC80Ny45Mi44Mi4yNVwvYXBpXC9sb2dpbiIsImlhdCI6MTY4MDE0ODU1NiwiZXhwIjoxNjgxNDQ0NTU2LCJuYmYiOjE2ODAxNDg1NTYsImp0aSI6ImNoVXhHcHBpT2xDYlN6OXciLCJzdWIiOjEsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.DC_a1lX4rxy4h3o5ufE6QPqPRod4nYENBtMDELyWTts","user_username":"1"}}
     *
     * @return_param api_token string api_token
     * @return_param name string 姓名
     *
     * @remark
     * @number 1
     */
    public function sendSmsLogin(Request $request)
    {

        $this->validate($request, [
            'phone' => 'required|numeric',
            'dxcodess' => 'required|numeric',
        ]);

        $res = AuthService::sendSmsLogin($request);

        if (is_array($res)) {
            // 登入成功
            return $this->success('success', 200, $res);
        }

        return $this->error($res);

    }

    /**
     * @catalog app端/用户相关
     * @title 注销账号
     * @description 注销账号
     * @method post
     * @url 47.92.82.25/api/closeAnAccount
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @return {"meta":{"status":200,"msg":"注销成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function closeAnAccount(Request $request)
    {

        $res = AuthService::closeAnAccount();

        if ($res) {
            return $this->success('close an account');
        }

        return $this->error($res);
    }

    /**
     * @catalog app端/用户相关
     * @title 安全退出
     * @description 安全退出
     * @method post
     * @url 47.92.82.25/api/safeWithdrawing
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @return {"meta":{"status":200,"msg":"退出成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function safeWithdrawing(Request $request)
    {

        $res = AuthService::safeWithdrawing();

        if ($res) {
            return $this->success($res);
        }

        return $this->error($res);
    }

    /**
     * @catalog app端/用户相关
     * @title 身份证正面图片上传
     * @description 身份证正面图片上传
     * @method post
     * @url 47.92.82.25/api/fontPhotoCard
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param id_front_photo 必选 string 身份证正面照片
     * @param id_back_photo 必选 string 身份证f反面照片
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function fontPhotoCard(Request $request)
    {
        $this->validate($request, [
            'id_front_photo' => 'required',
            'id_back_photo' => 'required',

        ]);

        $res = AuthService::fontPhotoCard($request);
        if ($res) {
            return $this->success($res);
        }

        return $this->error('error', '200', (array)$res);
    }

    /**
     * @catalog app端/用户相关
     * @title 身份证反面图片上传
     * @description 身份证反面图片上传
     * @method post
     * @url 47.92.82.25/api/backPhotoCard
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param id_back_photo 必选 mimes:jpeg,bmp,png,jpg 身份证正面照片
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function backPhotoCard(Request $request)
    {
        $this->validate($request, [
            'id_back_photo' => 'mimes:jpeg,bmp,png,jpg',
        ]);

        $res = AuthService::backPhotoCard($request);

        if ($res) {
            return $this->success($res);
        }

        return $this->error($res);
    }


    /**
     * @catalog app端/用户相关
     * @title 身份证正面识别
     * @description 身份证正面识别
     * @method post
     * @url 47.92.82.25/api/positiveRecognition
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param id_front_photo 必选 string 身份证正面照片路径
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function positiveRecognition(Request $request)
    {
        $this->validate($request, [
            'id_front_photo' => 'required',
        ]);

        $res = AuthService::positiveRecognition($request);

        return response($res);
    }

    /**
     * @catalog app端/用户相关
     * @title 身份证反面识别
     * @description 身份证反面识别
     * @method post
     * @url 47.92.82.25/api/negativeRecognition
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param id_back_photo 必选 string 身份证反面照片路径
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function negativeRecognition(Request $request)
    {
        $this->validate($request, [
            'id_back_photo' => 'required',
        ]);

        $res = AuthService::negativeRecognition($request);

        return $res;

    }

    /**
     * @catalog app端/用户相关
     * @title 用户认证信息录入
     * @description 用户认证信息录入
     * @method post
     * @url 47.92.82.25/api/authenticationEntry
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param id_number 必选 string 身份证
     * @param id_type 必选 string 证件类型(1身份证，2护照)
     * @param id_name 必选 string 身份证姓名
     * @param id_province 必选 string 所在省份
     * @param id_city 必选 string 所在城市
     * @param id_starttime 必选 string 证件开始时间
     * @param id_endtime 必选 string 证件结束时间
     * @param status 必选 string 认证状态，0表示未认证，1表示已认证
     * @param authenticate_time 必选 string 认证时间
     * @param result 必选 string 认证结果，0表示认证未通过，1表示认证通过
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function authenticationEntry(Request $request)
    {
        $this->validate($request, [
            'id_number'     => 'required',
            'id_type'       => 'required',
            'id_name'       => 'required',
            'id_province'   => 'required',
            'id_city'       => 'required',
            'id_starttime'  => 'required',
            'id_endtime'    => 'required',
            'status'        => 'required',
            'authenticate_time' => 'required',
            'result'        => 'required',
        ]);

        $res = AuthService::authenticationEntry($request);

        if ($res) {
            return $this->success($res);
        }

        return $this->error($res);

    }

    /**
     * @catalog app端/支付密码
     * @title 设置支付密码
     * @description 设置支付密码
     * @method post
     * @url 47.92.82.25/api/setPayPassword
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param pay_password 必选 int 支付密码（6位数字）
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function setPayPassword(Request $request)
    {
        $this->validate($request, [
            'pay_password'    => 'required|numeric',
        ]);

        // 密码格式 6-12位 字符加数字组合
        if (strlen($request->pay_password) == 6) {
            return $this->error('password_length_error');
        }

        $res = AuthService::setPayPassword($request);

        if ($res) {
            return $this->success($res);
        }

        return $this->error($res);

    }

    /**
     * @catalog app端/支付密码
     * @title 验证支付密码
     * @description 验证支付密码
     * @method post
     * @url 47.92.82.25/api/upPayPassword
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param pay_password 必选 int 支付密码（6位数字）
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function upPayPassword(Request $request)
    {
        $this->validate($request, [
            'pay_password'    => 'required|numeric',
        ]);

        // 密码格式 6-12位 字符加数字组合
        if (strlen($request->pay_password) == 6) {
            return $this->error('password_length_error');
        }

        $res = AuthService::upPayPassword($request);

        if ($res) {
            return $this->success($res);
        }

        return $this->error($res);

    }

    /**
     * @catalog app端/支付密码
     * @title 验证身份证号
     * @description 验证身份证号
     * @method post
     * @url 47.92.82.25/api/validateCard
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param id_card 必选 int 身份证号
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     *
     * @remark
     * @number 1
     */
    public function validateCard(Request $request)
    {
        $this->validate($request, [
            'id_card'    => 'required',
        ]);

        if (!validateIdCard($request->id_card)){
            return $this->error('card_format_wrong');
        }

        $res = AuthService::validateCard($request);

        if ($res == 'success') {
            return $this->success('successful_authentication');
        }
        return $this->error($res, '404', []);

    }

    /**
     * @catalog API/短信
     * @title 手机号验证
     * @description 手机号验证
     * @method post
     * @url 47.92.82.25/api/validateTel
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @param phone 必选 string 手机号
     * @param dxcodess 必选 string 验证码
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
    public function validateTel(Request $request)
    {
        $this->validate($request, [
            'phone'     => 'required|numeric',
            'dxcodess'  => 'required|numeric',
        ]);

        // 验证手机号格式
        if (!validatePhone($request->phone)) {
            return $this->error('phone_error');
        }

        $res = AuthService::validateTel($request);

        if ($res == 'success') {
            return $this->success('successful_authentication');
        }

        return $this->error($res, '404', []);

    }
}

