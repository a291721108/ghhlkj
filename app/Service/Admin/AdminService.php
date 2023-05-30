<?php

namespace App\Service\Admin;

use App\Http\Controllers\Common\LicenseController;
use App\Models\BusinessLicense;
use App\Models\Institution;
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

        // 判断用户密码是否正确
        $adminPassword = md5($adminPassword . $adminInfo->salt);

        if ($adminPassword !== $adminInfo->admin_password) {
            return 'password_error';
        }

        //判断该账号是否可以登录
        if ($adminInfo['status'] == InstitutionAdmin::INSTITUTION_ADMIN_STATUS_TWO) {
            return 'account_disabled';
        }

        $companyLicense = BusinessLicense::where('admin_id',$adminInfo->id)->first();

        //  登录成功 为管理员颁发token
        $token = Auth::guard('admin')->login($adminInfo);

        // 将token存在redis中 过期时间设置为1天
        $key = "oa_user_admin_token_" . $adminInfo->id;
        RedisService::set($key, $token);

        $companyName = Institution::where('admin_id',$companyLicense->id)->first();

        if (empty($companyName->institution_name)){
            $companyName = $companyLicense->companyName;
        }else{
            $companyName = $companyName->institution_name;
        }

        // redis存入异常抛错
        if (!RedisService::get($key)) {
            return 'redis_write_token_error';
        }

        return [
            'token'         => $token,
            'admin_phone'   => $adminInfo->admin_phone,
            'img'           => $adminInfo->admin_id,
            'companyName'   => $companyName,
            'created_at'    => hourMinuteSecond($adminInfo->created_at)
        ];
    }

    /**
     * 修改密码
     */
    public static function changePassword($request)
    {
        $adminPhone    = $request->admin_phone;
        $adminPassword = $request->admin_password;

        $salt          = rand(1,100);
        $adminPassword = md5($adminPassword . $salt);

        return InstitutionAdmin::where('admin_phone', $adminPhone)->update([
            'admin_phone'    => $adminPhone,
            'admin_password' => $adminPassword,
            'salt'           => $salt
        ]);
    }

    /**
     * 修改手机号
     */
    public static function changeTel($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();
        $adminPhone    = $request->admin_phone;

        return InstitutionAdmin::where('id', 1)->update([
            'admin_phone'    => $adminPhone,
        ]);
    }

    /**
     * 获取用户信息
     */
    public static function getAdminInfo()
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();

        $companyName = Institution::where('admin_id',$adminInfo->id)->first();
        $companyLicense = BusinessLicense::where('admin_id',$adminInfo->id)->first();
        if (empty($companyName->institution_name)){
            $companyName = $companyLicense->companyName;
        }else{
            $companyName = $companyName->institution_name;
        }
        return [
            'id'            => $adminInfo->id,
            'img'           => $adminInfo->img,
            'companyName'   => $companyName
        ];
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
            $companyLicense = BusinessLicense::where('admin_id',$useInfo->id)->first();

            //  登录成功 为用户颁发token
            $token = Auth::guard('admin')->login($useInfo);

            // 将token存在redis中 过期时间设置为1天
            $key = "gh_user_admin_token_" . $useInfo->id;
            RedisService::set($key, $token);

            // redis存入异常抛错
            if (!RedisService::get($key)) {
                return 'redis_write_token_error';
            }

            $companyName = Institution::where('admin_id',$companyLicense->id)->first();

            if (empty($companyName->institution_name)){
                $companyName = $companyLicense->companyName;
            }else{
                $companyName = $companyName->institution_name;
            }

            return [
                'token'         => $token,
                'id'            => $useInfo->id,
                'img'           => $useInfo->admin_id,
                'companyName'   => $companyName,
                'admin_name'    => $useInfo->admin_name,
                'admin_phone'   => $useInfo->admin_phone,
                'status'        => $useInfo->status,
                'created_at'    => hourMinuteSecond($useInfo->created_at),
            ];
        }

        return 'success';

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
            $userData = [
                'admin_phone'   => $request->admin_phone,
                'admin_img'     => env('APP_URL') . env('QRCODE_DIR') . '/my_img_default.png',
                'status'        => InstitutionAdmin::INSTITUTION_ADMIN_STATUS_ONE,
                'created_at'    => time(),
            ];
            $admin = InstitutionAdmin::insertGetId($userData);

            $licenseArr = [
                'admin_id'          => $admin,
                'creditCode'        => $request->creditCode,
                'companyName'       => $request->companyName,
                'companyType'       => $request->companyType,
                'businessAddress'   => $request->businessAddress,
                'legalPerson'       => $request->legalPerson,
                'legalPersonCard'      => $request->legalPersonCard,
                'legalPersonTel'       => $request->legalPersonTel,
                'proprietorName'       => $request->proprietorName,
                'proprietorCard'       => $request->proprietorCard,
                'proprietorTel'        => $request->proprietorTel,
                'businessScope'     => $request->businessScope,
                'registeredCapital' => $request->registeredCapital,
                'RegistrationDate'  => $request->RegistrationDate,
                'validPeriod'       => $request->validPeriod,
                'validFromDate'     => $request->validFromDate,
                'validToDate'       => $request->validToDate,
                'companyForm'       => $request->companyForm,
                'created_at'        => time(),
            ];

            $license = BusinessLicense::insert($licenseArr);

            // 提交事务
            DB::commit();

            return true;
        } catch (\Exception $e) {
            // 发生异常时回滚事务
            DB::rollBack();
            // 处理异常，例如记录日志或返回错误信息
            return 'sql_operation_failure';
        }
    }

    /**
     * 注销账户
     * @return string|bool
     */
    public static function clientCloseAnAccount()
    {
        $userInfo = InstitutionAdmin::getAdminInfo();
        $userInfo->status = InstitutionAdmin::INSTITUTION_ADMIN_STATUS_TWO;
        $userInfo->save();

        if ($userInfo) {
            return 'success';
        }
        return 'error';
    }
}









