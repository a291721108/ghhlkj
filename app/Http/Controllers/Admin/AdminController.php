<?php

namespace App\Http\Controllers\Admin;

use App\Libraries\AliyunOcr;
use App\Service\Admin\AdminService;
use Illuminate\Http\Request;
use App\Http\Controllers\Common\BaseController;
use AlibabaCloud\SDK\Ocrapi\V20210707\Ocrapi;
use AlibabaCloud\SDK\Ocrapi\V20210707\Models\RecognizeBusinessLicenseRequest;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Utils\Utils;
use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Exception;

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
     * @return {"meta":{"status":200,"msg":"成功"},"data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9nanhueS5jb21cL2FkbWluXC9sb2dpbiIsImlhdCI6MTY4MzUwNjg0NSwiZXhwIjoxNjg0ODAyODQ1LCJuYmYiOjE2ODM1MDY4NDUsImp0aSI6Imsyc0EwZnFFWWg1TUJkMzciLCJzdWIiOjEsInBydiI6ImM3NWFmYzE4YjA3YTc2ODRkNDZkMDI3ODM3N2I1ZTMyMjA0NzZjY2QifQ.gKUUP-jVuGxCC-cgxlIiYebA9Aj8ayLUR4sX3OOZB6c","admin_name":"admin","admin_phone":"17821211068","admin_institution_id":1,"created_at":1683506845}}
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

    //注册
    public function register(Request $request)
    {
        $this->validate($request, [
            'admin_phone' => 'required|numeric',
            'dxcodess'      => 'required|numeric',
        ]);

        $userInfo = AdminService::register($request);

        return $this->success('success', '200', $userInfo);
    }

//    /**
//     * 识别营业执照信息。
//     *
//     * @param  Request  $request
//     * @return \Illuminate\Http\JsonResponse
//     */
    private function createClient($accessKeyId, $accessKeySecret)
    {
        $config = new Config([
            // 必填，您的 AccessKey ID
            "accessKeyId" => $accessKeyId,
            // 必填，您的 AccessKey Secret
            "accessKeySecret" => $accessKeySecret
        ]);
        // 访问的域名
        $config->endpoint = "ocr-api.cn-hangzhou.aliyuncs.com";
        return new Ocrapi($config);
    }

    public function recognizeBusinessLicense(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|string|url'
        ]);

        // 工程代码泄露可能会导致AccessKey泄露，并威胁账号下所有资源的安全性。以下代码示例仅供参考，建议使用更安全的 STS 方式，更多鉴权访问方式请参见：https://help.aliyun.com/document_detail/311677.html
        $client = $this->createClient("LTAI5t5rmQ17MaK5H6MTJnPt", "ngXahrhVSI4QiCyynQwd099iuwWkSp");
        $recognizeBusinessLicenseRequest = new RecognizeBusinessLicenseRequest([
            "url" => $request->url
        ]);
        $runtime = new RuntimeOptions([]);
        try {
            $response = $client->recognizeBusinessLicenseWithOptions($recognizeBusinessLicenseRequest, $runtime);
            return response()->json($response);
        } catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
            }
            return response()->json([
                'error' => $error->getMessage()
            ], 400);
        }

    }
}
