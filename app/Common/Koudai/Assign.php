<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

class Assign extends Base
{
    const INVESTING_URL = "https://deposit.koudailc.com/user-project/project-investing-list?appVersion=6.4.1";
    const ASSIGN_URL = "https://deposit.koudailc.com/credit/assign?appVersion=6.4.1";
    const CANCEL_URL = "https://deposit.koudailc.com/credit/cancel-assignment?appVersion=6.4.1";
    private $product_id;
    private $money;
    private $pay_passwd = '';
    private $status_type = -1;
    private $invest_id;

    public function __construct($cookie, $pay_passwd)
    {
        parent::__construct(self::INVESTING_URL);
        $this->cookie = $cookie;
        $this->pay_passwd = $pay_passwd;
    }

    public function analyseList()
    {
        $this->curl->setCookie('SESSIONID', $this->cookie);
        $this->curl->post(self::INVESTING_URL, null);

        if ($this->curl->response->code == 0) {
            if ( count($this->curl->response->list) > 0 ) {
                foreach ($this->curl->response->list as $item) {
                    if ($item->project_id == $this->product_id) {
                        $this->status_type = $item->status_type;
                        $this->invest_id = $item->invest_id;
                        break;
                    }
                }
            }
        } else {
            $this->status_type = -2;
            $this->addDetail(json_encode($this->curl->response));
        }

        return !empty($this->invest_id) && in_array($this->status_type, [3, 4]);
    }

    public function cancel()
    {
        if ($this->status_type != 4) {
            return false;
        }

        $this->curl->setCookie('SESSIONID', $this->cookie);
        $this->curl->post(self::CANCEL_URL, [
            'invest_id' => $this->invest_id,
        ]);

        return $this->setResult($this->curl->response);
    }

    public function doJob()
    {
        if (!in_array($this->status_type, [3, 4])) {
            return false;
        }

        $this->curl->setCookie('SESSIONID', $this->cookie);
        $this->curl->post(self::ASSIGN_URL, [
            'invest_id' => $this->invest_id,
            'assign_fee' => $this->money,
            'is_split' => 0,
            'pay_password' => $this->pay_passwd,
        ]);

        return $this->setResult($this->curl->response);
    }

    public function setMoney($money) {
        $this->money = $money;
    }

    public function setProductId($product_id) {
        $this->product_id = $product_id;
    }

    public function isAssign() {
        return $this->status_type == -1;
    }

}