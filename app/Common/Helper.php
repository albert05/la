<?php

/**
 * 公共方法
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:01
 */

namespace App\Common;

class Helper
{

    /**
     * 加锁
     * @param $name
     * @return bool
     */
    public static function Lock($name) {
        $lock_file = '/tmp/_la_lock_console_' . $name;

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
        $lock_file = '/tmp/_la_lock_console_' . $name;

        if (!file_exists($lock_file)) {
            return false;
        }

        return unlink($lock_file);
    }
}