<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

use \Curl\Curl;

class Signin
{
    const SIGNIN_URL = "http://deposit.koudailc.com/user/login";
    const TYPE = 3; // 冒险型
    private $cookie;
    private $error_msg = '';


    public function __construct($cookie)
    {
        $this->cookie= $cookie;
    }

    public function run()
    {
        $curl = new Curl();
        $curl->setCookie('SESSIONID', $this->cookie);
        $curl->post(self::SIGNIN_URL, array(
            'type' => self::TYPE,
        ));

        if ($curl->response->code == 0) {
            return true;
        }

        $this->error_msg = $curl->response->message;
        return false;
    }

    public function getErrorMsg() {
        return $this->error_msg;
    }

}