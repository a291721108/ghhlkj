<?php
/**
 * redis操作类
 */

namespace App\Service\Common;

use Illuminate\Support\Facades\Redis;

class RedisService
{

    /**
     * @param $key
     * @param $value
     * @param $expire
     * @return mixed
     */
    public static function set($key, $value, $expire = null)
    {
        if (empty($expire)){
            $expire = 86400 + rand(10, 100);
        }

        if ($expire == "ALL") {
            // 不设置过期时间
            return Redis::set($key, $value);
        }

        return Redis::setex($key, $expire, $value);
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function del($key)
    {
        return Redis::del($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function get($key)
    {
        return Redis::get($key);
    }

    /**
     * 在队列头部插入一个元素
     * 返回队列长度
     */
    public  static function lPush($key, $value)
    {
        return Redis::lPush($key, $value);
    }

    /**
     * 删除并返回队列中的头元素。
     * @param unknown $key
     */
    public  static function lPop($key)
    {
        return Redis::lPop($key);
    }

    /**
     * 返回队列指定区间的元素
     * @param unknown $key
     * @param unknown $start
     * @param unknown $end
     */
    public static function lRange($key, $start, $end)
    {
        return Redis::lrange($key, $start, $end);
    }

    /**
     * 返回队列长度
     * @param unknown $key
     */
    public static function lLen($key)
    {
        return Redis::lLen($key);
    }

    /**
     * 在队列尾部插入一个元素
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public static function rPush($key, $value)
    {
        return Redis::rPush($key, $value);
    }

}
