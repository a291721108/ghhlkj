<?php

namespace App\Service\Admin;



use Illuminate\Support\Facades\Auth;

class AdminService
{

    /**
     * 登录
     * @param $request
     * @return array|string
     */
    public function login($request)
    {

        $adminPhone    = $request->admin_phone;
        $adminPassword = $request->admin_password;

        // 判断用户是否存在
        $adminInfo = CompanyAdmin::where('admin_phone', '=', $adminPhone)->first();

        if (!$adminInfo) {
            return 'user_not_exists';
        }

        // 判断用户秘密是正确
        $adminPassword = md5($adminPassword . $adminInfo->salt);

        if ($adminPassword !== $adminInfo->admin_password) {
            return 'password_error';
        }

        //判断该账号是否可以登录
        if ($adminInfo['status'] == CompanyAdmin::ADMIN_STATUS_TWO) {
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
            'token'       => $token,
            'bind_user'   => $adminInfo->bind_user,
            'admin_name'  => $adminInfo->admin_name,
            'admin_phone' => $adminInfo->admin_phone,
            'company_id'  => $adminInfo->company_id,
            'created_at'  => time()
        ];
    }

    /**
     * 修改密码
     */
    public static function changePassword($request)
    {
        $adminPhone    = $request->admin_phone;
        $adminPassword = $request->admin_password;

        $adminInfo     = CompanyAdmin::getAdminInfo();
        $adminPassword = md5($adminPassword . $adminInfo->salt);

        return CompanyAdmin::where('id', $adminInfo->id)->update([
            'admin_phone'    => $adminPhone,
            'admin_password' => $adminPassword
        ]);
    }


    /**
     * 获取用户信息
     */
    public static function getAdminInfo()
    {
        $adminInfo = CompanyAdmin::getAdminInfo();

        $roleList = CompanyRole::where('id', $adminInfo->role_id)->value('role_id');
        $menuList = [];

        if (!empty($roleList)) {
            $roleList = explode(',', $roleList);
            $menuList = Menu::whereIn('id', $roleList)->pluck('path')->toArray();
        }

        $userInfo = User::where('id', $adminInfo->bind_user)->select('avatar_url', 'openid', 'user_position', 'department_id')->first();

        $dept     = FunService::getDeptCategory();
        $position = FunService::getPositionCategory();

        return [
            'admin_name'    => $adminInfo->admin_name,
            'admin_phone'   => $adminInfo->admin_phone,
            'company_id'    => $adminInfo->company_id,
            'role'          => $adminInfo->role_id,
            'openid'        => $userInfo->openid,
            'avatar_url'    => $userInfo->avatar_url,
            'user_position' => $position[$userInfo->user_position],
            'department_id' => $dept[$userInfo->department_id],
            'role_list'     => implode(',', $menuList) ?? '',
            'roles'         => 'admin'
        ];
    }


}









