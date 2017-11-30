<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

class Order extends Base
{
    const ORDER_PRE_URL = "https://deposit.koudailc.com/project/invest-order?clientType=pc";
    const ORDER_DO_URL = "https://deposit.koudailc.com/project/invest-v2?clientType=pc";
    private $product_id;
    private $money;
    private $is_kdb_pay = 0;
    private $voucher_id = '';
    private $pay_passwd = '';

    public function __construct($cookie, $pay_passwd)
    {
        parent::__construct(self::ORDER_PRE_URL);
        $this->cookie = $cookie;
        $this->pay_passwd = $pay_passwd;
    }

    public function preJob()
    {
        $params = [
            'money' => $this->money,
        ];

        return $this->run($params);
    }

    public function doJob()
    {
        $this->preJob();
        if ($this->getErrorNo() != 0) {
            return false;
        }

        $this->setUrl(self::ORDER_DO_URL);
        $params = [
            'id' => $this->product_id,
            'money' => $this->money,
            'pay_password' => $this->pay_passwd,
            'is_kdb_pay' => $this->is_kdb_pay,
            'order_id' => $this->order_id,
            'voucher_id' => $this->voucher_id,
        ];

        return $this->run($params);
    }

    public function setMoney($money) {
        $this->money = $money;
    }

    public function setProductId($product_id) {
        $this->product_id = $product_id;
    }

    public function setIsKdbPay($is_kdb_pay) {
        $this->is_kdb_pay = $is_kdb_pay;
    }

    public function setVoucherId($voucher_id) {
        $this->voucher_id = $voucher_id;
    }
}