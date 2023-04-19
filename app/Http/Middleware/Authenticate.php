<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\JWTAuth;


class Authenticate extends BaseMiddleware
{

    /**
     * 用于区分前后端请求
     * @var string
     */
    public $guard;


    public function handle($request, Closure $next)
    {
        // 获取路径
        $path = $request->path();
        // 路径名称
        $this->guard= substr($path, 0, stripos($path,'/'));

        $this->checkForToken($request);

        // 使用 try 包裹
        try {

            // 检测用户的登录状态，如果正常则通过
            if (auth()->guard($this->guard)->check()) {
                return $next($request);
            }

            // 如果捕获到此异常，即代表 refresh 也过期了，用户无法刷新令牌，需要重新登录。
            $response = [
                'meta' => [
                    'status' => 400,
                    'msg'    => '未登录'
                ]
            ];

            return json_encode($response);
        } catch (TokenExpiredException $exception) {
            try {
                // 刷新用户的 token
                $token = $this->auth->refresh();
                // 使用一次性登录以保证此次请求的成功
                Auth::guard($this->guard)->onceUsingId($this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);

            } catch (JWTException $exception) {

                // 如果捕获到此异常，即代表 refresh 也过期了，用户无法刷新令牌，需要重新登录。
                $response = [
                    'meta' => [
                        'status' => 400,
                        'msg'    => $exception->getMessage()
                    ]
                ];

                return json_encode($response);
            }
        }

        // 在响应头中返回新的 token
        return $this->setAuthenticationHeader($next($request), $token);
    }
}
