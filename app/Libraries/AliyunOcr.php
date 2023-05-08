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
    public static function createClient(string $accessKeyId, string $accessKeySecret): Ocrapi
    {
        $config = new Config([
            'accessKeyId'     => $accessKeyId,
            'accessKeySecret' => $accessKeySecret,
        ]);

        $config->endpoint = "ocr-api.cn-hangzhou.aliyuncs.com";

        $client = new Ocrapi($config);

        return $client;
    }
    /**
     * 调用阿里云 OCR API 识别营业执照信息。
     *
     * @param  string  $accessKeyId
     * @param  string  $accessKeySecret
     * @param  string  $imageUrl
     * @return array|null
     */
    public static function recognizeBusinessLicense(string $accessKeyId, string $accessKeySecret, string $imageUrl): ?array
    {
        $client = self::createClient($accessKeyId, $accessKeySecret);
        $request = new RecognizeBusinessLicenseRequest([
            'imageURL' => $imageUrl,
        ]);
        $runtime = new RuntimeOptions([]);

        // 禁用 cURL

        try {
            $response = $client->recognizeBusinessLicenseWithOptions($request, $runtime);

            return $response->result;
        } catch (Exception $e) {
            var_dump($e);die();
            if (!($e instanceof TeaError)) {
                $e = new TeaError([], $e->getMessage(), $e->getCode(), $e);
            }
            return null;
        }
    }
}
