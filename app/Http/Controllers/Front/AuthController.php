<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Common\BaseController;
use App\Service\Front\AuthService;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function login(Request $request)
    {

        $res = AuthService::login($request);

        if (is_array($res)) {
            // 登入成功
            return $this->success('login_success', 200, $res);
        }

        return $this->error($res);
    }


    public function list(Request $request)
    {

        echo "123";
    }
}
