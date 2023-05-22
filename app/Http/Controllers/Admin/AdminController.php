<?php

namespace App\Http\Controllers\Admin;

use App\Service\Admin\AdminService;
use Illuminate\Http\Request;
use App\Http\Controllers\Common\BaseController;
use AlibabaCloud\Client\AlibabaCloud;


class AdminController extends BaseController
{
    /**
     * @catalog 商家端/管理员相关
     * @title 用户登录
     * @description 用户登录
     * @method post
     * @url 39.105.183.79/admin/login
     *
     * @param admin_phone 必选 string 账号
     * @param admin_password 必选 string 用户密码
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9nanhueS5jb21cL2FkbWluXC9sb2dpbiIsImlhdCI6MTY4NDMxNjQ1OCwiZXhwIjoxNjg1NjEyNDU4LCJuYmYiOjE2ODQzMTY0NTgsImp0aSI6Ik02N3ZpejdLdXptckxMM1MiLCJzdWIiOjEsInBydiI6ImM3NWFmYzE4YjA3YTc2ODRkNDZkMDI3ODM3N2I1ZTMyMjA0NzZjY2QifQ.ELjTvRI2PfJ2e1QY8v0eADJai9pD6YUqfAzwbMSfjdQ","admin_phone":"17821211068","created_at":1684316458}}
     *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     * @return_param token string token
     * @return_param admin_name string 姓名
     * @return_param admin_phone string 手机号
     * @return_param admin_institution_id string 机构id
     *
     * @remark
     * @number 1
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'admin_phone'    => 'required',
            'admin_password' => 'required',
        ]);

        $data = AdminService::login($request);

        if (is_array($data)) {
            return $this->success('success', '200', $data);
        }
        return $this->error($data, '404', []);
    }

    /**
     * @catalog 商家端/管理员相关
     * @title 修改密码
     * @description 修改密码
     * @method post
     * @url 39.105.183.79/admin/changePassword
     *
     * @param admin_phone 必选 int 公司名称
     * @param admin_password 必选 string 账号
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[]}
     *
     * @return_param status int 状态码
     * @return_param msg string 消息
     * @return_param data [] 数组
     *
     *
     * @remark
     * @number 4
     */
    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'admin_phone'    => 'required',
            'admin_password' => 'required',
        ]);

        $res = AdminService::changePassword($request);

        if ($res) {
            return $this->success('success', '200', []);
        }
        return $this->error('error');
    }

    /**
     * @catalog 商家端/管理员相关
     * @title 获取用户信息
     * @description 获取用户信息
     * @method post
     * @url 39.105.183.79/admin/getAdminInfo
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2FkbWluXC9sb2dpbiIsImlhdCI6MTY0ODExMzM3NSwiZXhwIjoxNjQ4MTE2OTc1LCJuYmYiOjE2NDgxMTMzNzUsImp0aSI6IkR2dDVLNmtTdDZ5V0NhdDMiLCJzdWIiOjgsInBydiI6ImFjYmI0NTAwY2UzMTc3YjA5ZWZiMzNiMTFlMzIxY2NkMmIzM2M3YWMifQ.mzzLjIsgnOB1kLb1RhirL3hmKVI636BtmoGVrT-Uoes","admin_name":"张三","admin_phone":"17865992641","company_id":1,"created_at":1648113375}}
     * *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     * @return_param token string token
     * @return_param admin_name string 姓名
     * @return_param admin_phone string 手机号
     * @return_param company_id string 公司ID
     *
     * @remark
     * @number 2
     */
    public function getAdminInfo()
    {
        $userInfo = AdminService::getAdminInfo();

        return $this->success('success', '200', $userInfo);
    }

    /**
     * @catalog 商家端/管理员相关
     * @title 手机号验证码校验
     * @description 手机号验证码校验
     * @method post
     * @url 39.105.183.79/admin/codeLogin
     *
     * @param admin_phone 必选 int 手机号
     * @param dxcodess 必选 int 验证码
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3RcL2FkbWluXC9sb2dpbiIsImlhdCI6MTY0ODExMzM3NSwiZXhwIjoxNjQ4MTE2OTc1LCJuYmYiOjE2NDgxMTMzNzUsImp0aSI6IkR2dDVLNmtTdDZ5V0NhdDMiLCJzdWIiOjgsInBydiI6ImFjYmI0NTAwY2UzMTc3YjA5ZWZiMzNiMTFlMzIxY2NkMmIzM2M3YWMifQ.mzzLjIsgnOB1kLb1RhirL3hmKVI636BtmoGVrT-Uoes","admin_name":"张三","admin_phone":"17865992641","company_id":1,"created_at":1648113375}}
     * *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     * @return_param token string token
     * @return_param admin_name string 姓名
     * @return_param admin_phone string 手机号
     * @return_param company_id string 公司ID
     *
     * @remark
     * @number 2
     */
    public function codeLogin(Request $request)
    {
        $this->validate($request, [
            'admin_phone' => 'required|numeric',
            'dxcodess'      => 'required|numeric',
        ]);

        $userInfo = AdminService::codeLogin($request);

        if ($userInfo == 'success') {
            return $this->success('code_check_success');

        }
        return $this->error($userInfo);
    }

    /**
     * @catalog 商家端/管理员相关
     * @title 营业执照识别
     * @description 营业执照识别
     * @method post
     * @url 39.105.183.79/admin/addLicense
     *
     * @param Url 必选 string 营业执照图片路径
     * @param admin_phone 必选 int 手机号
     * @param legalPersonCard 必选 int 法人身份证
     * @param legalPersonTel 必选 int 法人手机号
     * @param proprietorName 必选 int 经营者姓名
     * @param proprietorCard 必选 int 经营者身份证
     * @param proprietorTel 必选 int 经营者手机号
     *
     * @return {"meta":{"status":404,"msg":"失败"},"data":[]}
     *
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     * @return_param token string token
     * @return_param admin_name string 姓名
     * @return_param admin_phone string 手机号
     * @return_param company_id string 公司ID
     *
     * @remark
     * @number 2
     */
    // 营业执照识别
    public function addLicense(Request $request)
    {
        $this->validate($request, [
//            'Url'           => 'required',
            'admin_phone'   => 'required',
            'legalPersonCard'   => 'required',
            'legalPersonTel'   => 'required',
            'proprietorName'   => 'required',
            'proprietorCard'   => 'required',
            'proprietorTel'   => 'required',
        ]);

        $userInfo = AdminService::addLicense($request);

        if ($userInfo == 'success') {
            return $this->success('success', '200', []);

        }
        return $this->error('error');
    }
}
