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
        $options->protocol = 'HTTPS';
        $options->gatewayHost = 'openapi-sandbox.dl.alipaydev.com';
        $options->signType = 'RSA2';

        $options->appId = '9021000122682218';
        // 为避免私钥随源码泄露，推荐从文件中读取私钥字符串而不是写入源码中
        $options->merchantPrivateKey = 'MIIEogIBAAKCAQEAgkAYSMpObBSXloj2DFa4NWkIUJIQGZat11goybPKZqsuwZ5C9BmAHOzpoGchvcU3nDSi5Gys9pSSkzpZZ59x4Xro0VJXTrO+cyf8NKJ7ycYuuxPdzOBvGPvcaRAFGHKslgT0xiWQci3hdT8a/URDsJLKaRsbmiIEQsHqXNlWepSxggALQ5oSkV2yrbVIVMZ94GGLzTlCgY8vDjo+HzzZrZQqG+t9DWD5aZxivNTwALxCMR4zkt2HXd3Ejizv0f2asS5fyJhtbqowT9yPz2FIWlK19B7oFlt01yJqDYeov3a0TIcISK/1d/aZk2SkvCIWxEDApy2YmifLd+QpbPXZfQIDAQABAoIBAHsLweVB+UPxUCNNz+NoKS2Lw+cZlUwXJLNC/YVO6+6B2PHgaK/hRz4MMcjupNl57kcLErdoUx2+zZl4je1um06/piHY/9HBzDFNnNy8guTi7FKfOfHKNCoOBPLbx3SJILG4jOSDqCm1XkA9FWodizTL95vDmBfL+up19skwKJozcn9ezEkyLYtMzGetratpkeZo2UiDyg2Ihoy0uKiuFq0/m/MQIq3Q7z6M6SQVg7Uqd1R2Z6fdDneIwaMORzmbkVxmPVAMPgcYIJxH8+jk/ehV/Z19KeYU4/AeUe0qvPH4IOopE3XcGR+FaLW/f7RqkHnK7c60/LK8trqAfohjWgECgYEAz6lB6k+Z3JAnA1OH0hcMWn5rJ0wNCCUPXQojyu3KgJ6+MP9znPqEyEmcA3HHtpSemzJwVA/H0nNu/Jr32GryBly4Fb2BBC4hPetClA0Z4uNmZS9EYZG5KgJIrHkIfsGkfwC323qlNwtyqJ6nwlgsj/ohQ7SY8ZkLtJpKSmyDam0CgYEAoJHZPRILCHcQrZ0WlQASEujILqVfn1jALPZQLyB7coPgjbEkdXfFFOkbGfz1C1T3yoa+S+L+uTrq7CfvnZr30aA3EVpG0lS4jI6ToaXLQUl0xpUsy8XFnNBomegAoJgU4kp6VjD71E6vG9BInxdWmAF7x91/ZsQBr6Dv322ZwVECgYBg5JXH8XpWKsC5UZlXEsAF3WS8AkU2Xqxreha3Ufqhxu/xRtA4F8ArAAWWqdlIvNHAkmlNH8vZy1im7tvkFAp3o32VT0XKoIeML6ByGibQ8c/OOJ7Mc8UU79ne995Z3pqVU8110CUUqPfH67dU+/VW/JvWzoZqfEG09CTj0vwChQKBgCMWW+NFexW5dvKJPjMHFev95CiVSKT4bt4kYPc7YN5wRPSRfgcRSga8vYhbR2zXf2JJOXI5wzHU1xsNywWkpHyxRvdKw+kYTE+ipE4Rfa3hkFwIowZQTFNtEz52fRSaxw1/+uW0xILrQsaQKB5jqi+DO3o8Q6fAMyIex+wJ9ixRAoGAT2L4byPxEPiOnHgaHIX8U2ArPGGmjugQBf1Ern0ChRE2iSGUR+ZPmkCNlREtgK5NiQ/8jZPhwrFbhD6+tzSTQm3Rqs7XW/ls9lyjQrzM9PTGj3jOFw9glK3zwai1DxcZDIjVocQVAwON2cmia7I5SAvOrxkyGCTgJjV56KHAoHU=';

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

        // 创建支付实例
        $alipay = Factory::setOptions(self::getOptions());

        $paymentPage = $alipay->payment()->app()->pay($request->order_number,$request->product_name,$request->amount);

        // 解析支付参数
        $params = [];
        parse_str($paymentPage->body, $params);

        // 构建支付宝 App 的支付链接
        $baseUrl = 'alipays://platformapi/startapp';
        $appId = '20000067';
        $orderSuffix = 'ALIPAYHK';

        $payUrl = $baseUrl . '?appId=' . $appId . '&orderSuffix=' . $orderSuffix;
        foreach ($params as $key => $value) {
            $paramString = urlencode($key) . '=' . urlencode($value);
            $payUrl .= '&' . $paramString;
        }

        // 构造支付参数
//        $return_url = 'https://openapi-sandbox.dl.alipaydev.com';
        // 调用支付接口生成支付页面
//        $paymentPage = $alipay->payment()->page()->pay($request->order_number,$request->product_name,$request->amount,$return_url);

        return $payUrl;




        dd();
        // 创建新的订单，省略具体实现
        $factory = Factory::setOptions(self::getOptions());

        try {
            //2. 发起API调用（以支付能力下的统一收单交易创建接口为例）
//            $result = Factory::payment()->App()->pay("iPhone6 16G", "2020******5526001", "88.88");
            //创建支付实例

            $payUrl = Factory::payment()->app()->pay("iPhone6 16G", "70501111111S001111119", "9.00");
            $responseChecker = new ResponseChecker();

            //3. 处理响应或异常
            if ($responseChecker->success($payUrl)) {
                $data = urldecode($payUrl->body);
//                $parameters = [];
//                parse_str($data, $parameters);
//                // 构建支付请求URL
//                $url = 'alipays://platformapi/startapp?appId=' . $parameters['app_id'] .
//                    '&timestamp=' . urlencode($parameters['timestamp']) .
//                    '&bizContent=' . urlencode($parameters['biz_content']) .
//                    '&sign=' . urlencode($parameters['sign']) .
//                    '&signType=' . $parameters['sign_type'] .
//                    '&appCertSn=' . $parameters['app_cert_sn'] .
//                    '&alipayRootCertSn=' . $parameters['alipay_root_cert_sn'];


                $payUrlParams = [];
                parse_str($data, $payUrlParams);
                $payLink = 'alipays://platformapi/startapp?' . http_build_query($payUrlParams);
                return $this->success('success', 200, ['url' => $payLink]);
            } else {
                echo "调用失败，原因：" . $payUrl->msg . "，" . $payUrl->subMsg . PHP_EOL;
            }
        } catch (\Exception $e) {
            echo "调用失败，". $e->getMessage(). PHP_EOL;;
        }
    }

}
