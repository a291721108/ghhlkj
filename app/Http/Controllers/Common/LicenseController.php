<?php

namespace App\Http\Controllers\Common;

use AlibabaCloud\Client\AlibabaCloud;
use Alipay\EasySDK\Kernel\Config;
use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Kernel\Util\ResponseChecker;
use Illuminate\Http\Request;
use PHPUnit\Runner\Exception;
use Yansongda\Pay\Pay;

class LicenseController extends BaseController
{
    /**
     * @catalog API/公共
     * @title 营业执照识别
     * @description 营业执照识别
     * @method post
     * @url 47.92.82.25/api/recognizeBusinessLicense
     *
     * @param url 必选 string 图片地址
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":{"RegistrationDate":"2023年03月06日","businessAddress":"晋中市山西综改示范区晋中开发区大学城产业园区金科山西智慧科技城D16-02号","businessScope":"一般项目:技术服务、技术开发、技术咨询、技术交流、技术转让、技术推广:信息技术咨询服务:网络技术服务:软件开发;科技中介服务;信息咨询服务(不含许可类信息咨询服务):软件外包服务;软件销售;互联网销售(除销售需要许可的商品);数据处理和存储支持服务;互联网数据服务;数据处理服务;家政服务;日用百货销售:日用品销售;企业管理咨询。(除依法须经批准的项目外,凭营业执照依法自主开展经营活动)","companyForm":"","companyName":"山西光晖互联科技有限公司","companyType":"有限责任公司(自然人投资或控股)","creditCode":"91140791MACD74A11P","legalPerson":"范磊","registeredCapital":"壹佰万圆整","validFromDate":"20230306","validPeriod":"","validToDate":"29991231"}}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param data array 返回数据
     *
     * @remark
     * @number 3
     */
    public function recognizeBusinessLicense(Request $request)
    {
        $this->validate($request, [
            'Url'           => 'required',
        ]);

        AlibabaCloud::accessKeyClient(env('ALIYUN_SMS_AK'), env('ALIYUN_SMS_AS'))
            ->regionId('cn-hangzhou')
            ->asDefaultClient();

        try {
            $response = AlibabaCloud::rpc()
                ->product('ocr-api')
                ->version('2021-07-07')
                ->action('RecognizeBusinessLicense')
                ->method('POST')
                ->host('ocr-api.cn-hangzhou.aliyuncs.com')
                ->options([
                    'query' => [
                        'Url' => $request->Url,
                    ],
                ])
                ->request();

            $query = $response->Data;
            $result = json_decode($query);

            return $this->success('success', '200', (array)$result->data);
        } catch (\Exception $e) {

            return $this->error('error','404',['data' => $e->getMessage()]);
        }
    }

    public function getOptions()
    {
        $options = new Config();
        $options->protocol = 'https';
        $options->gatewayHost = 'openapi.alipaydev.com/gateway.do';
        $options->signType = 'RSA2';

        $options->appId = '9021000122682218';
        // 为避免私钥随源码泄露，推荐从文件中读取私钥字符串而不是写入源码中
        $options->merchantPrivateKey = 'MIIEowIBAAKCAQEAiniGWPj5XIvQouMYUpyDR6P80gNHk6Trq1i+ZAGa87v0wj1q/rPIi6hrc5seUrNhnE5oMA/klT+9TCtPWXCEZAL6SfbuY41xykmI80pwv4i7PWmwEsEKRYOjX+Z4j6X0uYIPTIa+jTTYbdWs27DMXFPdNQvP+pOIrOirVGXLPrugjA4nd7NaEH1acE1poK7EQaQ7GKDFv2u8Po0e43Woq4dzOG2LU+xEFSO6ScBC1cDsKled+t+k1CtTk8yomGhD1umQuGjgAkGmp8OwslBOmfVctL2pnnUd5Bcf0rnsn/hH9sDd/UyMgoZscoqoZndwDHjrj1WUDupB/7g53p0g7QIDAQABAoIBAE5RStNJxmgEoDVwslIPOeUsKBN0TWiBb9XS9KRFkClo1k+CQ2DZuITc9iFFy8nEsWGhqyX75zJPAbbyDAgvLoIOeReadUyTNJfQLYhFQy3hnN1oSHDjA/c7NA1KokfE+nxtxk9nKqFdEUhWAVWkUoGp4URecPxts3Dwi+7JQEIzMdmFm0PmE/iPHcxv4iyjADeewR1NCvh9lS1FCMwiF0OG5esE15ZsBlcV99CO6Lx250puIu3/VEnmIWirNjZdRYTOc8+VllnvIPBLo/bLi2cRN6XYwNttNa2x/vOKWWEEPWNBxDtay+z9OgCCk20GtLKhFRBIBVkV0ogCXvm7vAECgYEAwNyNjwueye3jf7zJbXGnpQt4CayKNIMUDaB81+lDNjtRJIcDuxwY7rRMwtKDCtCCbbsIVzc4nchI6Ryfiz8+0GRdxAAAi8B7F/13iOLdru8aiMTJ2pXZHMoL+/c0Ja3zJovTiwCOM7seplXZFLyfQcs+Exhv1rpm9jPEsX/j9wECgYEAt82QEvrarmiRutoY0++6tdteCBR+N4ZObgeZWS51am+6QhwwKU1T7rwCVvF8C1/2W6wqgRL91Q5lPe8y0DFZmnDFdsbVEGsmC9WI1yIu3Q+p1fQbdeJo2QgIqcFdrBHsY5PjTnZ2Qzt+hEn0TNO+qA+WjRCLNn7ZFxaOF2aude0CgYBCX+WMNIiaoHeqb7O3KeVzhOX0FmCeP/p30iMFP+90y6dadekPzVS7WmwMpNyarTYmQ7dUJNokW1jUeZhjGQoqOFCY8xM9BaqXkBFCmCmJWhr8tRVvWCEXqmXDesmEqkBDpN3SOge2wBCFWIhUfbIlt6gOdFuRQCfNVfW8zPINAQKBgQCM5sjLxAJgMUmGNCtUTTKUttYe25bmec8mCi1EcJkSPxRKGdHR17XADeC9ReIR1j7Fh/YNfMGZ0q9KC1SoxSwreMfnhgVP0NQZvlhok4vZa2iG29sxQ5+cwMvjVpi4kATnUTevrqB6aeFTCF5/htNhgpGnEkemPxes212SEuUrAQKBgAH80grAbSkO6HfSpqJMhc8MJ54dOttXGzTQ+/XqFRlCgP0e3MWI9QO4BiglZra1HmLC8pW3Gt9FmatVmkoFz8rESWnP9jFRkRzCIq8wvEL+TLMzn2uvwXfgVgLi0g5kN4JFEsZt/ugG4Ldcl5QKE30m/z5lQFR9D6dbbQhY0jiy';

        $publicPath = base_path('public/');
        $options->alipayCertPath        = $publicPath.'certificate/alipayPublicCert.crt';
        $options->alipayRootCertPath    = $publicPath.'certificate/alipayRootCert.crt';
        $options->merchantCertPath      = $publicPath.'certificate/appPublicCert.crt';

        //注：如果采用非证书模式，则无需赋值上面的三个证书路径，改为赋值如下的支付宝公钥字符串即可
        // $options->alipayPublicKey = '<-- 请填写您的支付宝公钥，例如：MIIBIjANBg... -->';
        //可设置异步通知接收服务地址（可选）
        //如果需要使用文件上传接口，请不要设置该参数
//        $options->notifyUrl = "<-- 请填写您的支付类接口异步通知接收服务地址，例如：https://www.test.com/callback -->";

        //可设置AES密钥，调用AES加解密相关接口时需要（可选）
//        $options->encryptKey = "<-- 请填写您的AES密钥，例如：aa4BtZ4tspm2wnXLb1ThQA== -->";
        return $options;
    }

    // todo  支付宝支付
    public function create(Request $request)
    {
        // 创建新的订单，省略具体实现
        $factory = Factory::setOptions(self::getOptions());

        try {
            //2. 发起API调用（以支付能力下的统一收单交易创建接口为例）
//            $result = Factory::payment()->App()->pay("iPhone6 16G", "2020******5526001", "88.88");
            $payUrl = $factory::payment()->app()->pay("iPhone6 16G", "70501111111S001111119", "9.00");
            $responseChecker = new ResponseChecker();

            //3. 处理响应或异常
            if ($responseChecker->success($payUrl)) {
                $data = urldecode($payUrl->body);
                $parameters = [];
                parse_str($data, $parameters);
                // 构建支付请求URL
                $url = 'alipay://platformapi/startapp?appId=' . $parameters['app_id'] . '&timestamp=' . urlencode($parameters['timestamp']) . '&bizContent=' . urlencode($parameters['biz_content']) . '&sign=' . urlencode($parameters['sign']) . '&signType=' . $parameters['sign_type'] . '&appCertSn=' . $parameters['app_cert_sn'] . '&alipayRootCertSn=' . $parameters['alipay_root_cert_sn'];

                return $this->success('success',200,['url'=>$url]);
            } else {
                echo "调用失败，原因：". $payUrl->msg."，".$payUrl->subMsg.PHP_EOL;
            }
die();
            $payParams = $result->body; // 假设 $result 是支付宝支付接口的响应

            // 处理支付结果
            if (!empty($payParams)) {
                // 解析响应内容
                parse_str($payParams, $parsedResponse);

                // 提取参数值
                $appId = $parsedResponse['app_id'];
                $timestamp = $parsedResponse['timestamp'];
                $signType = $parsedResponse['sign_type'];
                $bizContent = $parsedResponse['biz_content'];
                $sign = $parsedResponse['sign'];

                $payUrl = Factory::payment()->common()->create($appId, $timestamp, $signType, $bizContent, $sign);
                // 返回支付链接给移动应用
                echo $payUrl;
            } else {
                // 支付请求失败，处理错误信息
                $errorMessage = $response->msg ?? 'Unknown error';

                // 返回错误信息给移动应用
                echo $errorMessage;
            }

        } catch (Exception $e) {
            echo "调用失败，". $e->getMessage(). PHP_EOL;;
        }


        die();
        // 推送通知到商家手机上
        $order = [
            'out_trade_no' => 'order_no', // 自定义的订单号
            'total_amount' => '0.01', // 支付金额
            'subject' => '订单标题', // 订单标题
        ];

        $alipay = Pay::alipay(config('alipay'));
        $response = $alipay->app($order)->send();
        parse_str($response, $data);

        $sign = $data['sign'];
        unset($data['sign']); // 从数组中移除签名字段
        ksort($data); // 按键名进行排序

        $signString = urldecode(http_build_query($data));

//        $sandboxPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkg28jbaufPSZTOUtv9wchiwhw3p+8+KIxDJz6B7azDt2ZW3ou47STJHEa3G2La9MQnN8uSP4gMeyzO0E2N14BQUzdkkR6yElMVM40bkVduD9P53bHOks9vUDlZCcp8GXZCh+ZsKUfesiN/+qn3RUPipiYoUw1VaKn7oJ3CruGtbbwlw+o80Wae0osJXcbPECpbmgG6ctqGxlecvZ2KTTKIGF8//llDmL6S2+YKPlewINm5y9lvBO/ZoMzslkloWPxB+QFEJF6A9bvjepFaTsu88D7aijT0qYxDo+WAa9z/g8oGUG21+VDrbvjfrsOLP3FlS7t4lTS6HgN0TkD9cmpQIDAQAB';
        $sandboxPublicKey = config('alipay.alibb');
        $formattedPublicKey = "-----BEGIN PUBLIC KEY-----\n" . $sandboxPublicKey . "\n-----END PUBLIC KEY-----";

        $publicKey = openssl_get_publickey($formattedPublicKey);
        $errorMsg = openssl_error_string();

dd($errorMsg);
        if ($publicKey === false) {
            // 公钥加载失败
            return 'error';
        }

        $isValid = openssl_verify($signString, base64_decode($sign), $publicKey, OPENSSL_ALGO_SHA256);

        openssl_free_key($publicKey);
        if ($isValid === 1) {
            // 签名验证通过
            // 可以在这里进行订单状态更新等业务处理
            return 'success';
        } elseif ($isValid === 0) {
            // 签名验证失败
            return 'fail';
        } else {
            // 验证过程发生错误
            return 'error';
        }
    }

}
