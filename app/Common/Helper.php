<?php

/**
 * 公共方法
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:01
 */

namespace App\Common;

USE DB;

class Helper
{

    /**
     * 加锁
     * @param $name
     * @return bool
     */
    public static function Lock($name) {
        $lock_file = '/tmp/_la_lock_console_' . $name . '.log';

        if (file_exists($lock_file)) {
            return false;
        }

        return touch($lock_file);
    }

    /**
     * 解锁
     * @param $name
     * @return bool
     */
    public static function unlock($name) {
        $lock_file = '/tmp/_la_lock_console_' . $name . '.log';

        if (!file_exists($lock_file)) {
            return false;
        }

        return unlink($lock_file);
    }

    /**
     * 过滤命令参数
     * @param $signature
     * @return string
     */
    public static function filterSignature($signature) {
        return trim(preg_replace('/\{(.*)\}/', '', $signature));
    }

    /**
     * 获取命令执行路径
     * @return string
     */
    public static function getBash() {
        $global_config = DB::table('workconfigs')
            ->where('work_id', 'global')
            ->lists('value', 'key');

        $cmd = '';
        $script_path = '';
        foreach ($global_config as $k => $v) {
            if ($k == 'cmd') {
                $cmd = $v;
            } elseif($k == 'script_path') {
                $script_path = $v;
            }
        }


        return " " . $cmd . " " . $script_path . " ";
    }

    /**
     * 获取日志输出路径
     * @param $id
     * @return string
     */
    public static function getLogOutput($id, $user_id) {
        $global_config = DB::table('workconfigs')
            ->where('work_id', 'global')
            ->lists( 'value', 'key');

        $log_path = '/tmp/';
        foreach ($global_config as $k => $v) {
            if ($k == 'log_path') {
                $log_path = $v;
            }
        }

        $path = $log_path . $id;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return " 1>>" . $log_path . $id . "/" . date('Y-m-d') . "_{$user_id}.log 2>&1 & ";
    }

    /**
     * 获取当前毫秒级时间
     * @return float
     */
    public static function getMicrotime() {
        list($usec, $sec) = explode(" ", microtime());
        return floatval(date("Gis")) + $usec;
    }

    /**
     * 转换时间格式
     * 转换时11/01/2017 12:00 AM间格式
     * @param $timeStr
     * @return false|string
     */
    public static function analyzeTimeStr($timeStr) {
        return date("Y-m-d H:i:s", strtotime(preg_replace('/^(\d{2})\/(\d{2})\/(\d{4})(.*)$/', '$3\/$1\/$2$4', $timeStr)));
    }

    public static function getUserAgent($is_rand = false) {
        $user_agent_pool = [
            "Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_3 like Mac OS X) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.0 Mobile/14G60 Safari/602.1",
        ];

        if ($is_rand) {
            $idx = rand(0, count($user_agent_pool) - 1);
            return $user_agent_pool[$idx] ?? $user_agent_pool[0];
        }

        return $user_agent_pool[0];
    }

    public static function analyzeUrl($url) {
        $query = parse_url($url, PHP_URL_QUERY);

        if ($query == false) {
            return false;
        }

        $query_arr = [];
        $query_list = explode("&", $query);
        foreach ($query_list as $value) {
            $item = explode("=", $value);
            $query_arr[$item[0]] = $item[1];
        }

        return $query_arr;
    }

    /**
     * @return array
     */
    public static function randIp(){
        $ip_long = [
            ['607649792', '608174079'], //36.56.0.0-36.63.255.255
            ['1038614528', '1039007743'], //61.232.0.0-61.237.255.255
            ['1783627776', '1784676351'], //106.80.0.0-106.95.255.255
            ['2035023872', '2035154943'],  //121.76.0.0-121.77.255.255
            ['2078801920', '2079064063'], //123.232.0.0-123.235.255.255
            ['-1950089216', '-1948778497'], //139.196.0.0-139.215.255.255
            ['-1425539072', '-1425014785'], //171.8.0.0-171.15.255.255
            ['-1236271104', '-1235419137'], //182.80.0.0-182.92.255.255
            ['-770113536', '-768606209'], //210.25.0.0-210.47.255.255
            ['-569376768', '-564133889'], //222.16.0.0-222.95.255.255
        ];

        $rand_key = mt_rand(0, 9);
        $ip= long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
        $headers['CLIENT-IP'] = $ip;
        $headers['X-FORWARDED-FOR'] = $ip;

        $headerArr = [];
        foreach( $headers as $n => $v ) {
            $headerArr[] = $n .':' . $v;
        }

        return $headerArr;
    }

}