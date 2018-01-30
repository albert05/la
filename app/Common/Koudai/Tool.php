<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

use App\Common\Helper;
use \Curl\Curl;

class Tool
{
    private $url;
    private $url_list = [
        0 => "https://deposit.koudailc.com/user-project/project-investing-list",
        1 => "https://deposit.koudailc.com/voucher/show-vouchers?page=1&pageSize=15&state=1",
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

        return Helper::jsonFormat($this->curl->response);
    }

    /** 将数组元素进行urlencode
     * @param String $val
     */
    public function jsonFormatProtect(&$val){
        if($val!==true && $val!==false && $val!==null){
            $val = urlencode($val);
        }
    }

}