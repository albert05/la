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
        return trim(preg_replace('/\{(\w+)\}/', '', $signature));
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
}