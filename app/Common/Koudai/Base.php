<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/30
 * Time: 00:37
 */

namespace App\Common\Koudai;

use App\Common\Helper;
use \Curl\Curl;

class Base
{
    protected $curl;
    protected $url = "";
    protected $cookie;
    protected $error_no = 0;
    protected $error_msg = '';
    protected $time_point = '';
    protected $order_id = '';
    protected $user_agent;

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

    public function setUrl($url) {
        $this->url = $url;
    }

    public function setUserAgent($user_agent) {
        $this->user_agent = $user_agent;
    }

    public function setResult($response) {
        if (is_string($response)) {
            return false;
        }
        if (property_exists($response, 'order_id')) {
            $this->order_id = $response->order_id;
        }
        if (property_exists($response, 'code')) {
            $this->error_no = $response->code;
        }
        if (property_exists($response, 'message')) {
            $this->error_msg = $response->message;
        }

        return $response->code == 0;
    }

    public function getCookie() {
        return $this->cookie;
    }

    public function setCookie($cookie) {
        $this->cookie = $cookie;
    }

    public function setTimePoint($time_point) {
        $this->time_point = $time_point;
    }

    public function run($params)
    {
        $this->wait();

        if ($this->cookie) {
            $this->curl->setCookie('SESSIONID', $this->cookie);
        }
        $this->curl->post($this->url, $params);

        if (!$this->cookie) {
            $this->setCookie($this->curl->response->sessionid);
        }
        return $this->setResult($this->curl->response);
    }

    protected function wait() {
        if (!$this->time_point) {
            return true;
        }

        $now = Helper::getMicrotime();

        while ($now < $this->time_point) {
            usleep(10); // 0.01毫秒
            $now = Helper::getMicrotime();
        }

        echo $now . PHP_EOL;
        return true;
    }
}