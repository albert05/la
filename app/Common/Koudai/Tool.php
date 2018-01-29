<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

use \Curl\Curl;

class Tool
{
    private $url;
    private $url_list = [
        0 => "https://deposit.koudailc.com/user-project/project-investing-list",
    ];

    public function __construct($cookie)
    {
        $this->cookie = $cookie;
    }

    public function setUrl($idx) {
        $this->url = $this->url_list[$idx];
    }

    public function doJob()
    {
        $params = [
        ];

        $this->curl = new Curl();
        $this->curl->setCookie('SESSIONID', $this->cookie);
        $this->curl->post($this->url, $params);

        return json_encode($this->curl->response);
    }

}