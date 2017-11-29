<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

use \Curl\Curl;

class Earn extends Base
{
    const EARN_URL = "https://deposit.koudailc.com/user-level/earn";
    const TYPE = 3; // 冒险型

    public function __construct($cookie)
    {
        parent::__construct(self::EARN_URL);
        $this->cookie= $cookie;
    }

    public function run()
    {
        $curl = new Curl();
        $curl->setCookie('SESSIONID', $this->cookie);
        $curl->post(self::SIGNIN_URL, array(
            'type' => self::TYPE,
        ));

        return $this->setError($curl->response);
    }
}