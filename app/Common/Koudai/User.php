<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

use \Curl\Curl;

class User
{
    const LOGIN_URL = "http://deposit.koudailc.com/user/login";
    private $username;
    private $password;
    private $cookie;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function login()
    {
        $curl = new Curl();
        $curl->post(self::LOGIN_URL, array(
            'username' => $this->username,
            'password' => $this->password,
        ));

        var_dump($curl->responseHeaders);
    }

    public function getCookie() {
        return $this->cookie;
    }

}