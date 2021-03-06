<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

use App\Common\Helper;

class Order extends Base
{
    const ORDER_PRE_URL = "https://deposit.koudailc.com/project/invest-order?clientType=android&appVersion=6.8.7&deviceId=357052090656869&deviceName=SM-G9500&osVersion=8.0.0&appMarket=sanxing"; //?clientType=pc
    const ORDER_DO_URL = "https://deposit.koudailc.com/deposit/project/invest?clientType=android&appVersion=6.8.7&deviceId=357052090656869&deviceName=SM-G9500&osVersion=8.0.0&appMarket=sanxing"; //-v2//deposit/project/invest
    const KD_KEY = "**kdlc**";
    private $product_id;
    private $money;
    private $is_kdb_pay = 0;
    private $voucher_id = '';
    private $pay_passwd = '';
    private $is_wait_sjk = 0;

    public function __construct($cookie, $pay_passwd)
    {
        parent::__construct(self::ORDER_PRE_URL);
        $this->cookie = $cookie;
        $this->pay_passwd = $pay_passwd;
    }

    public function preJob()
    {
        $this->setUrl(self::ORDER_PRE_URL);

        $params = [
            'is_deposit' => 1,
            'money' => $this->money,
        ];

        return $this->run($params);
    }

    public function doJob()
    {
        $this->preJob();
        if ($this->getErrorNo() != 0) {
            $this->addDetail("pre order failed: " . Helper::getMicrotime() . "\n");
            return false;
        }

        if ($this->is_wait_sjk) {
            $spider = new Spider(sprintf(Spider::ORDER_RECORD_URL, $this->product_id));
            $spider->waitOrder();
            $this->addDetail($spider->getDetail());
        }

        $this->setUrl(self::ORDER_DO_URL);
        $params = [
            'id' => $this->product_id,
            'money' => $this->money,
           // 'pay_password' => $this->pay_passwd,
            //'is_kdb_pay' => $this->is_kdb_pay,
            'order_id' => $this->order_id,
            'voucher_id' => $this->voucher_id,
            'redpacket_money' => 0.00,
        ];

        // 加密校验
        ksort($params);
        $tmpArr = [];
        foreach ($params as $k => $val) {
            $tmpArr[] = "$k=$val";
        }
        $params['invest_sign'] = base64_encode(implode("&", $tmpArr) . self::KD_KEY);

        $ret = $this->run($params);
        $this->addDetail("end_time: " . Helper::getMicrotime() . "\n");
        return $ret;
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

    public function setIsWaitSjk($is_wait_sjk) {
        $this->is_wait_sjk = $is_wait_sjk;
    }
}