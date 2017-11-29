<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/30
 * Time: 00:37
 */

namespace App\Common\Koudai;

use \Curl\Curl;

class Base
{
    protected $curl;
    protected $url = "";
    protected $cookie;
    protected $error_no = 0;
    protected $error_msg = '';

    public function __construct($url)
    {
        $this->curl = new Curl();
        $this->url = $url;
    }

    public function getErrorNo() {
        return $this->error_no;
    }

    public function getErrorMsg() {
        return $this->error_msg;
    }

    public function setError($response) {
        $this->error_no  = $response->code;
        $this->error_msg = $response->message ?? '';

        return $response->code == 0;
    }

    public function getCookie() {
        return $this->cookie;
    }

    public function setCookie($cookie) {
        $this->cookie = $cookie;
    }

    public function run($params)
    {
        if ($this->cookie) {
            $this->curl->setCookie('SESSIONID', $this->cookie);
        }
        $this->curl->post($this->url, $params);

        if (!$this->cookie) {
            $this->setCookie($this->curl->response->sessionid);
        }
        return $this->setError($this->curl->response);
    }
}