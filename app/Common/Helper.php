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
        return trim(preg_replace('/\{(\w+\?)\}/', '', $signature));
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
    public static function getLogOutput($id) {
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

        return " 1>>" . $log_path . $id . "/" . date('Y-m-d') . ".log 2>&1 & ";
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
}