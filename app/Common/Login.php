<?php

/**
 * 登录
 * Created by PhpStorm.
 * User: albert
 * Date: 2018/01/07
 * Time: 18:01
 */

namespace App\Common;

use App\Common\Koudai\KdUser;
use App\Models\UserInfo;
use Illuminate\Support\Facades\Redis;

class Login
{
    const KD_LOGIN_SESSION_KEY = "kd.login.user.session.%s";
    private $expire_time = 300;

    public function kd_login($user_id) {

        $key = sprintf(self::KD_LOGIN_SESSION_KEY, $user_id);
        if ($cookie = Redis::get($key)) {
            return ["is_cache" => true, "cookie" => $cookie];
        }
        $user = UserInfo::where('user_key', $user_id)->firstOrFail();

        $kd_user = new KdUser($user->user_name, $user->password);

        $kd_user->login();
        $cookie = $kd_user->getCookie();
        Redis::setex($key, $this->expire_time, $cookie);

        return ["is_cache" => false, "cookie" => $cookie];
    }

    public function setExpireTime($expire_time) {
        $this->expire_time = $expire_time;
    }
}