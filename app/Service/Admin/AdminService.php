<?php

namespace App\Service\Admin;

use App\Http\Controllers\Common\LicenseController;
use App\Models\BusinessLicense;
use App\Models\InstitutionAdmin;
use App\Models\UserSend;
use App\Service\Common\RedisService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
     * 手机号验证码校验
     */
    public static function codeLogin($request)
    {
        $code = $request->dxcodess;
        $adminPhone = $request->admin_phone;

        // 判断是否有验证吗
        $sendInfo = UserSend::where('phone', '=', $adminPhone)->orderBy('id', 'desc')->first();

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
        $useInfo = InstitutionAdmin::where('admin_phone', '=', $adminPhone)->where('status', '=', InstitutionAdmin::INSTITUTION_ADMIN_STATUS_ONE)->first();

        if ($useInfo){
            //  登录成功 为用户颁发token
            $token = Auth::guard('admin')->login($useInfo);

            // 将token存在redis中 过期时间设置为1天
            $key = "gh_user_admin_token_" . $useInfo->id;
            RedisService::set($key, $token);

            // redis存入异常抛错
            if (!RedisService::get($key)) {
                return 'redis_write_token_error';
            }

            return [
                'token'         =>$token,
                'id'            => $useInfo->id,
                'admin_name'    => $useInfo->admin_name,
                'admin_phone'   => $useInfo->admin_phone,
                'status'        => $useInfo->status,
                'created_at'    => hourMinuteSecond($useInfo->created_at),
            ];
        }

        return 'code_check_success';

    }

    /**
     * 营业执照识别
     */
    public static function addLicense($request)
    {
        // 判断用户是否存在
        $useInfo = InstitutionAdmin::where('admin_phone', '=', $request->admin_phone)->where('status', '=', InstitutionAdmin::INSTITUTION_ADMIN_STATUS_ONE)->first();
        if ($useInfo){
            return 'error';
        }

        // 开启事务
        DB::beginTransaction();
        try {
            // 执行一些数据库操作
            $data = [
                'admin_phone'   => $request->admin_phone,
                'status'        => InstitutionAdmin::INSTITUTION_ADMIN_STATUS_ONE,
                'created_at'    => time(),
            ];
            $admin = InstitutionAdmin::insertGetId($data);

            $businessLicense = LicenseController::recognizeBusinessLicense($request->Url);
            $licenseArr = [
                'admin_id'          => $admin,
                'creditCode'        => $businessLicense['creditCode'],
                'companyName'       => $businessLicense['companyName'],
                'companyType'       => $businessLicense['companyType'],
                'businessAddress'   => $businessLicense['businessAddress'],
                'legalPerson'       => $businessLicense['legalPerson'],
                'legalPersonCard'      => $request->legalPersonCard,
                'legalPersonTel'       => $request->legalPersonTel,
                'proprietorName'       => $request->proprietorName,
                'proprietorCard'       => $request->proprietorCard,
                'proprietorTel'        => $request->proprietorTel,
                'businessScope'     => $businessLicense['businessScope'],
                'registeredCapital' => $businessLicense['registeredCapital'],
                'RegistrationDate'  => $businessLicense['RegistrationDate'],
                'validPeriod'       => $businessLicense['validPeriod'],
                'validFromDate'     => $businessLicense['validFromDate'],
                'validToDate'       => $businessLicense['validToDate'],
                'companyForm'       => $businessLicense['companyForm'],
                'created_at'        => time(),
            ];

            BusinessLicense::insert($licenseArr);
            // 提交事务
            DB::commit();

            return "success";
        } catch (\Exception $e) {
            // 发生异常时回滚事务
            DB::rollBack();
            // 处理异常，例如记录日志或返回错误信息
            return 'sql_operation_failure';
        }
    }
}









