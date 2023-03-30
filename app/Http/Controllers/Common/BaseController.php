<?php

namespace App\Http\Controllers\Common;

use Laravel\Lumen\Routing\Controller;
use App\Exceptions\ErrorCode;

class BaseController extends Controller
{

    /**
     * @param string $msg
     * @param int $code
     * @param array $data
     * @return false|string
     */
    public function success(string $msg, int $code = 200, array $data = [])
    {
        $msg = (new ErrorCode())->getSuccessMsg($msg);

        $response = [
            'meta' => [
                'status' => $code,
                'msg'    => $msg
            ],
            'data' => is_array($data) ? $data:[]
        ];

        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return false|string
     */
    public function error(string $msg, int $code = 404, array $data = [])
    {
        $msg = (new ErrorCode())->getErrorMsg($msg);

        $response = [
            'meta' => [
                'status' => $code,
                'msg'    => $msg
            ],
            'data' => $data
        ];

        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

}
