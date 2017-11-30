<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

class Shake extends Base
{
    const SHAKE_URL = "https://deposit.koudailc.com/daily-shake/daily-shake-award";

    public function __construct($cookie)
    {
        parent::__construct(self::SHAKE_URL);
        $this->cookie= $cookie;
    }

    public function doJob()
    {
        return $this->run(null);
    }
}