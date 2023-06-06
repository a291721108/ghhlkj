<?php

namespace App\Http\Controllers\Common;

use AlibabaCloud\Client\AlibabaCloud;
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

    // todo  支付宝支付
    public function create(Request $request)
    {
        // 创建新的订单，省略具体实现

        // 推送通知到商家手机上
        $order = [
            'out_trade_no' => 'order_no', // 自定义的订单号
            'total_amount' => '0.01', // 支付金额
            'subject' => '订单标题', // 订单标题
        ];

        $alipay = Pay::alipay(config('alipay'));
        return $alipay->app($order)->send();
    }

    public function handlePaymentNotify(Request $request)
    {
        $alipay = Pay::alipay(config('alipay'));

        try {
            $data = $alipay->verify(); // 验证支付回调数据的签名

            // 处理支付成功逻辑，更新订单状态等操作

            return $alipay->success(); // 返回给支付宝成功的响应
        } catch (\Exception $e) {
            // 处理支付失败逻辑

            return $alipay->fail(); // 返回给支付宝失败的响应
        }
    }
}
