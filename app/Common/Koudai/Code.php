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
    private $filename;

    public function __construct($cookie)
    {
        parent::__construct(self::CODE_URL);
        $this->cookie = $cookie;
        $rand = time() + "" + rand(100000, 999999);
        $this->filename = "la-assets/img/captcha/captcha_{$rand}.png";
    }

    public function refresh() {
        $this->curl->setCookie('SESSIONID', $this->cookie);
        $this->curl->post(self::REFRESH_URL, null);

        $this->url_params = $this->curl->response->url;
    }

    public function doJob()
    {

        $this->createImage();

//        $err_i = 0;
//        for($i = 0; $i < $this->ver_count; $i++) {
//
//            try {
//                $image = new Image($this->filename);
//                $this->codes[] = implode("", $image->find());
//                if ($this->is_debug) {
//                    $image->draw();
//                }
//            } catch (\Exception $e) {
//                echo $e->getMessage() . "\n";
//                $err_i++;
//            } finally {
//                $i--;
//                if ($err_i > 5) {
//                    return false;
//                }
//            }
//
//        }

        return true;
    }

    public function getCode() {
        if ($this->is_debug) {
            var_dump($this->codes);
        }
        if (count(array_unique($this->codes)) >= 4) {
            $tmp = array_unique($this->codes);
            return current($tmp);
        }

        return '';
    }

    public function setDebug($debug) {
        $this->is_debug = $debug;
    }

    public function getFileName() {
        return $this->filename;
    }

    private function createImage() {
        $fp = fopen(public_path($this->filename), 'wb');
        $this->curl->setCookie('SESSIONID', $this->cookie);
        $this->curl->setOpt(CURLOPT_FILE, $fp);
        $this->curl->setOpt(CURLOPT_HEADER, 0);
        $this->curl->setOpt(CURLOPT_FOLLOWLOCATION, 1);
        $this->curl->post(sprintf($this->url, $this->url_params), null);
        fclose($fp);
    }

}