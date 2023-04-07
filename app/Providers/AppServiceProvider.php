<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 依赖注册JWT鉴权机制
        $this->app->register(\Tymon\JWTAuth\Providers\LumenServiceProvider::class);

        // 依赖注册redis缓存类
        $this->app->register(\Illuminate\Redis\RedisServiceProvider::class);

        // 依赖注入跨域组件
        $this->app->register(\Fruitcake\Cors\CorsServiceProvider::class);
    }
}
