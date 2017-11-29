<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

use \Curl\Curl;

class User extends Base
{
    const LOGIN_URL = "http://deposit.koudailc.com/user/login";
    private $username;
    private $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function login()
    {
        $params = [
            'username' => $this->username,
            'password' => $this->password,
        ];

        return $this->run($params);
    }
}