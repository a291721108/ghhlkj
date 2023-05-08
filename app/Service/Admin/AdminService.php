<?php

namespace App\Service\Admin;

use App\Models\InstitutionAdmin;
use App\Models\UserSend;
use App\Service\Common\RedisService;
use Illuminate\Support\Facades\Auth;

class AdminService
{

    /**
     * 登录
     * @param $request
     * @return array|string
     */
    public static function login($request)
    {

        $adminPhone    = $request->admin_phone;
        $adminPassword = $request->admin_password;

        // 判断用户是否存在
        $adminInfo = InstitutionAdmin::where('admin_phone', '=', $adminPhone)->first();

        if (!$adminInfo) {
            return 'user_not_exists';
        }

        // 判断用户秘密是正确
        $adminPassword = md5($adminPassword . $adminInfo->salt);

        if ($adminPassword !== $adminInfo->admin_password) {
            return 'password_error';
        }

        //判断该账号是否可以登录
        if ($adminInfo['status'] == InstitutionAdmin::INSTITUTION_ADMIN_STATUS_TWO) {
            return 'account_disabled';
        }

        //  登录成功 为管理员颁发token
        $token = Auth::guard('admin')->login($adminInfo);

        // 将token存在redis中 过期时间设置为1天
        $key = "oa_user_admin_token_" . $adminInfo->id;
        RedisService::set($key, $token);

        // redis存入异常抛错
        if (!RedisService::get($key)) {
            return 'redis_write_token_error';
        }

        return [
            'token'                 => $token,
            'admin_name'            => $adminInfo->admin_name,
            'admin_phone'           => $adminInfo->admin_phone,
            'admin_institution_id'  => $adminInfo->admin_institution_id,
            'created_at'            => time()
        ];
    }

    /**
     * 修改密码
     */
    public static function changePassword($request)
    {
        $adminPhone    = $request->admin_phone;
        $adminPassword = $request->admin_password;

        $adminInfo     = InstitutionAdmin::getAdminInfo();
        $adminPassword = md5($adminPassword . $adminInfo->salt);

        return InstitutionAdmin::where('id', $adminInfo->id)->update([
            'admin_phone'    => $adminPhone,
            'admin_password' => $adminPassword
        ]);
    }


    /**
     * 获取用户信息
     */
    public static function getAdminInfo()
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();

        return $adminInfo;
    }

    /**
     * 商家端注册
     */
    public static function register($request)
    {
        $code = $request->dxcodess;
        $adminPhone = $request->admin_phone;

        // 判断是否有验证吗
        $sendInfo = UserSend::where('phone', '=', $adminPhone)->orderBy('id', 'desc')->first();

        if (!$sendInfo) {
            return 'phone_error';
        }

        //  验证吗是否过期 有效期限五分钟
//        if (time() >= ($sendInfo->send_time + 300)) {
//            return 'code_expired';
//        }

        // 验证码错误
        if ($sendInfo->code !== intval($code)) {
            return 'code_error';
        }

        // 判断用户是否存在
        $useInfo = InstitutionAdmin::where('admin_phone', '=', $adminPhone)->where('status', '=', InstitutionAdmin::INSTITUTION_ADMIN_STATUS_ONE)->first();

        if ($useInfo){
            return 'The user does not exist.';
        }


        dd($useInfo);
        return "123";

    }


}









