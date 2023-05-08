<?php

namespace App\Libraries;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\SDK\Ocrapi\V20210707\Ocrapi;
use AlibabaCloud\SDK\Ocrapi\V20210707\Models\RecognizeBusinessLicenseRequest;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Utils\Utils;
use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Exception;

class AliyunOcr
{
    /**
     * 使用 AccessKeyID 和 AccessKeySecret 初始化阿里云 OCR API 客户端。
     *
     * @param  string  $accessKeyId
     * @param  string  $accessKeySecret
     * @return Ocrapi
     */
    private function createClient(string $accessKeyId, string $accessKeySecret): Ocrapi
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


    /**
     * 调用阿里云 OCR API 识别营业执照信息。
     *
     * @param  string  $accessKeyId
     * @param  string  $accessKeySecret
     * @param  string  $imageUrl
     * @return array|null
     */
    public function recognizeBusinessLicense(string $accessKeyId, string $accessKeySecret, string $imageUrl): ?array
    {
        // 工程代码泄露可能会导致AccessKey泄露，并威胁账号下所有资源的安全性。以下代码示例仅供参考，建议使用更安全的 STS 方式，更多鉴权访问方式请参见：https://help.aliyun.com/document_detail/311677.html
        $client = $this->createClient($accessKeyId, $accessKeySecret);
        $recognizeBusinessLicenseRequest = new RecognizeBusinessLicenseRequest([
            "url" => $imageUrl
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
