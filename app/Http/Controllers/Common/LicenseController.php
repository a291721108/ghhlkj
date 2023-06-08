<?php

namespace App\Http\Controllers\Common;

use AlibabaCloud\Client\AlibabaCloud;
use Alipay\EasySDK\Kernel\Config;
use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Kernel\Util\ResponseChecker;
use Illuminate\Http\Request;
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


    protected function getOptions()
    {
        $options = new Config();
        $options->protocol = 'https';
        $options->gatewayHost = 'https://openapi.alipay.com/gateway.do';
        $options->signType = 'RSA2';

        $options->appId = config("alipay.app_id");

        // 为避免私钥随源码泄露，推荐从文件中读取私钥字符串而不是写入源码中
        $options->merchantPrivateKey = config("alipay.private_key");

//        $options->alipayCertPath = '<-- 请填写您的支付宝公钥证书文件路径，例如：/foo/alipayCertPublicKey_RSA2.crt -->';
//        $options->alipayRootCertPath = '<-- 请填写您的支付宝根证书文件路径，例如：/foo/alipayRootCert.crt" -->';
//        $options->merchantCertPath = '<-- 请填写您的应用公钥证书文件路径，例如：/foo/appCertPublicKey_2019051064521003.crt -->';

        //注：如果采用非证书模式，则无需赋值上面的三个证书路径，改为赋值如下的支付宝公钥字符串即可
         $options->alipayPublicKey = config("alipay.public_key");

        //可设置异步通知接收服务地址（可选）
//        $options->notifyUrl = "https://openapi-sandbox.dl.alipaydev.com/gateway.do";

        return $options;
    }

    // todo  支付宝支付
    public function create(Request $request)
    {
        // 创建新的订单，省略具体实现

        // 推送通知到商家手机上

        Factory::setOptions($this->getOptions());

        try {
            //2. 发起API调用（以支付能力下的统一收单交易创建接口为例）
            $result = Factory::payment()->common()->create("iPhone6 16G", "20200326235526001", "88.88", "2088002656718920");
            $responseChecker = new ResponseChecker();
            //3. 处理响应或异常
            if ($responseChecker->success($result)) {
                echo "调用成功". PHP_EOL;
            } else {
                echo "调用失败，原因：". $result->msg."，".$result->subMsg.PHP_EOL;
            }
        } catch (Exception $e) {
            echo "调用失败，". $e->getMessage(). PHP_EOL;;
        }








        die();
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
