<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

use App\Common\Captcha\Image;

class Code extends Base
{
    const CODE_URL = "http://deposit.koudailc.com%s";
    const REFRESH_URL = "http://deposit.koudailc.com/user/captcha?refresh";
    private $ver_count = 3;
    private $codes = [];
    private $url_params;
    private $is_debug = false;

    public function __construct($cookie)
    {
        parent::__construct(self::CODE_URL);
        $this->cookie = $cookie;
    }

    public function refresh() {
        $this->curl->setCookie('SESSIONID', $this->cookie);
        $this->curl->post(self::REFRESH_URL, null);

        $this->url_params = $this->curl->response->url;
    }

    public function doJob()
    {

        $err_i = 0;
        for($i = 0; $i < $this->ver_count; $i++) {
            $this->curl->setCookie('SESSIONID', $this->cookie);
            $this->curl->post(sprintf($this->url, $this->url_params), null);

            try {
                $image = new Image($this->curl->response);
                $this->codes[] = implode("", $image->find());
                if ($this->is_debug) {
                    $image->draw();
                }
            } catch (\Exception $e) {
                echo $e->getMessage() . "\n";
                $err_i++;
            } finally {
                $i--;
                if ($err_i > 2) {
                    return false;
                }
            }

        }

        return true;
    }

    public function getCode() {
        if ($this->is_debug) {
            var_dump($this->codes);
        }
        if (count(array_unique($this->codes)) != 1) {
            return '';
        }

        return $this->codes[0];
    }

    public function setDebug($debug) {
        $this->is_debug = $debug;
    }

}