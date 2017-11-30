<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

class Exchange extends Base
{
    const EXCHANGE_URL = "https://deposit.koudailc.com/user-order-form/convert";
    private $product_id = 4;  // 4: %1加息券
    private $code;
    private $prize_number = 1;

    public function __construct($cookie)
    {
        parent::__construct(self::EXCHANGE_URL);
        $this->cookie = $cookie;
    }

    public function doJob()
    {
        $params = [
            'id' => $this->product_id,
            'imgcode' => $this->code,
            'prize_number' => $this->prize_number,
        ];

        return $this->run($params);
    }

    public function setCode($code) {
        $this->code = $code;
    }

    public function setProductId($product_id) {
        $this->product_id = $product_id;
    }

    public function setPrizeNumber($prize_number) {
        $this->prize_number = $prize_number;
    }
}