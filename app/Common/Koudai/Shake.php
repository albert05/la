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
    private $shake_count = 3;

    public function __construct($cookie)
    {
        parent::__construct(self::SHAKE_URL);
        $this->cookie= $cookie;
    }

    public function doJob()
    {
        $count = 0;
        while ($count < $this->shake_count || $this->error_no != 0) {
            $this->run(null);
            $count++;
        }

        return $this->error_no == 0;
    }

    public function setShakeCount($count) {
        $this->shake_count = $count;
    }
}