<?php
/**
 * 助手函数 正则匹配手机号
 */

use Illuminate\Support\Str;

/**
 * 验证手机号
 */
if (!function_exists('validatePhone')) {

    function validatePhone($phoneNum): bool
    {
        $role = "/^1(3[0-9]|4[01456879]|5[0-35-9]|6[2567]|7[0-8]|8[0-9]|9[0-35-9])\d{8}$/";


        if (preg_match($role, $phoneNum)) {
            return true;
        }

        return false;
    }
}

/**
 * 密码格式 同时由数字和字母组成
 */
if (!function_exists('validatePassword')) {

    function validatePassword($password): bool
    {
        $role = "/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9a-zA-Z]+$/";

        if (preg_match($role, $password)) {
            return true;
        }

        return false;
    }
}

/**
 * 密码格式 转换时间格式
 */
if (!function_exists('formattingTime')) {

    function formattingTime($time): string
    {
        return date("Y-m-d H", $time);
    }
}

/**
 *  统一回参接口 将null 转化位空字符串
 */
if (!function_exists('nullToStr')) {

    function nullToStr($arr)
    {
        foreach ($arr as $k => $v) {
            if (is_null($v)) {
                $arr[$k] = '';
            }
            if (is_array($v)) {
                $arr[$k] = nullToStr($v);
            }
        }

        return $arr;
    }
}

/**
 * 获取配置路径
 */
if (!function_exists('config_path')) {

    function config_path($path = ''): string
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

/**
 * 获取用户IP
 */
if (!function_exists('getClientIp')) {
    function getClientIp()
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] as $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }

        return $ip;
    }
}

/**
 * 用户排序
 */
if (!function_exists('makeKey')) {

    function makeKey($userInfo = []): array
    {
        $arr = [];
        foreach ($userInfo as $k => $v) {

            $arr[$v['department_id']][] = $v;
        }

        return $arr;
    }
}

/**
 * 转换安时间区间查询
 */
if (!function_exists('searchData')) {

    function searchData($searchData): array
    {
        $searchData = explode('——', $searchData);

        foreach ($searchData as &$v) {
            $v = strtotime($v);
        }

        return $searchData;
    }
}

/**
 * 时间戳转年月日
 */
if (!function_exists('ytdTampTime')) {

    function ytdTampTime($ytdTampTime)
    {
        if ($ytdTampTime == true) {
            $timeGo = date('Y-m-d', $ytdTampTime);
        } else {
            $timeGo = $ytdTampTime;
        }
        return $timeGo;
    }
}

/**
 * 时间戳转年月日 时分
 */
if (!function_exists('timestampTime')) {

    function timestampTime($timestampTime)
    {
        if ($timestampTime == true) {
            $timeGo = date('Y-m-d H:i', $timestampTime);
        } else {
            $timeGo = $timestampTime;
        }
        return $timeGo;
    }
}

/**
 * 时间戳转年月日 时分秒
 */
if (!function_exists('hourMinuteSecond')) {

    function hourMinuteSecond($hourMinuteSecond)
    {
        if ($hourMinuteSecond == true) {
            $timeGo = date('Y-m-d H:i:s', $hourMinuteSecond);
        } else {
            $timeGo = $hourMinuteSecond;
        }
        return $timeGo;
    }
}

/**
 * 时间转时分
 */
if (!function_exists('timesTamp')) {

    function timesTamp($timestampTime)
    {
        if ($timestampTime == true) {
            $res = date('Y-m-d H:i', $timestampTime);
            $timeGo = substr($res, 11, 16);
        } else {
            $timeGo = $timestampTime;
        }
        return $timeGo;
    }
}

/**
 * 获取时间戳内日期
 * @param $start_time
 * @param $end_time
 * @return array
 */
if (!function_exists('periodDate')) {

    function periodDate($start_time, $end_time): array
    {
        $i = 0;
        $arr = [];

        while ($start_time <= $end_time) {
            $arr[$i] = date('Y-m-d', $start_time);
            $start_time = strtotime('+1 day', $start_time);
            $i++;
        }

        return $arr;
    }
}

/**
 * 时分秒转试时间戳
 */
if (!function_exists('hmsToTimestamp')) {

    function hmsToTimestamp($time): string
    {
        $data = date_parse($time);
        return $data['hour'] * 3600 + ($data['minute'] / 60) * 3600;
    }
}

/**
 * get 请求
 */
if (!function_exists('curlGet')) {

    function curlGet($url)
    {
        $header = array(
            'Accept: application/json',
        );
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 超时设置,以秒为单位
        curl_setopt($curl, CURLOPT_TIMEOUT, 100);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        //执行命令
        $data = curl_exec($curl);

        // 显示错误信息
        if (curl_error($curl)) {
            print "Error: " . curl_error($curl);
        }

        curl_close($curl);

        return $data;
    }
}

/**
 * post 请求
 */
if (!function_exists('curlPost')) {

    function curlPost($url, $postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}


/**
 * PHP 计算方差
 */
if (!function_exists('variance')) {
    function variance($arr)
    {
        $length = count($arr);
        $average = array_sum($arr) / $length;
        $count = 0;

        foreach ($arr as $v) {
            $count += pow($average - $v, 2);
        }

        $variance = $count / $length;

        return [
            'variance' => $variance,
            'average' => $average
        ];
    }
}

/**
 * 计算时间差
 */
if (!function_exists('timeDifference')) {
    function timeDifference($arr)
    {
        $diff = [];
        foreach ($arr as $k => $v) {
            if ($k != 0) {
                $diff[] = abs(strtotime($v) - strtotime($arr[$k - 1]));
            }
        }

        return $diff;
    }
}

/**
 * 时间转换处理
 * 8:30 =  8.5
 */
if (!function_exists('standardiseTimeFormat')) {
    function standardiseTimeFormat($arr, $time = [])
    {
        if (count($arr) > 1) {
            foreach ($arr as $v) {
                if (strpos($v, ":") !== false) {
                    list($workTimeH, $workTimeF) = explode(':', $v);
                    $time[] = number_format($workTimeH + ($workTimeF / 60), 2);
                } else {
                    $time[] = $v;
                }
            }
            return $time;
        }
    }
}

/**
 * 获取用户IP
 */
if (!function_exists('getClientIp')) {
    function getClientIp()
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] as $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }

        return $ip;
    }
}
/**
 * 身份证验证
 */
if (!function_exists('validateIdCard')) {
    function validateIdCard(string $idCard): bool
    {

        // 身份证号必须为18位
        if (strlen($idCard) !== 18) {
            return false;
        }

        // 身份证号前17位必须为数字
        if (!preg_match('/^\d{17}$/', substr($idCard, 0, 17))) {
            return false;
        }

        // 校验码校验
        $checkCode = getCheckCode(substr($idCard, 0, 17));
        if ($checkCode !== substr($idCard, 17, 1)) {
            return false;
        }

        return true;
    }
}

/**
 * 身份证加权
 */
if (!function_exists('getCheckCode')) {
    function getCheckCode(string $idCardNo): string
    {
        // 加权因子
        $factor = [
            7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2,
        ];

        // 校验码对应值
        $code = [
            1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2,
        ];

        $sum = 0;
        for ($i = 0; $i < 17; $i++) {
            $sum += intval(substr($idCardNo, $i, 1)) * $factor[$i];
        }

        return strval($code[$sum % 11]);
    }
}

/**
 * 时间月份差
 */

if (!function_exists('getMonthDiff')) {
    function getMonthDiff($timestamp1,$timestamp2)
    {
        $date1 = new \DateTime(date("Y-m-d H:i:s",$timestamp1));
        $date2 = new \DateTime(date("Y-m-d H:i:s",$timestamp2));

        $interval = $date1->diff($date2);
        $months = $interval->m;
        return $months;
    }
}
