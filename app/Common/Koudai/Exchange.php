<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

use App\Common\Helper;
use App\Models\Task;

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

    public function doJob($task_id = '')
    {
        $this->waitIt($task_id);

        $params = [
            'id' => $this->product_id,
            'imgcode' => $this->code,
            'prize_number' => $this->prize_number,
        ];

        echo $this->code . PHP_EOL;

        $this->curl->setCookie('SESSIONID', $this->cookie);
        $this->curl->post($this->url, $params);

        var_dump($this->curl->response);

        return $this->setResult($this->curl->response);
    }

    protected function waitIt($task_id) {
        if (!$this->time_point) {
            return true;
        }

        $now = Helper::getMicrotime();

        while ($now < $this->time_point) {
            usleep(10000); // 10毫秒
            $now = Helper::getMicrotime();
            if (!$this->code) {
                $task = Task::where(['id' => $task_id])->first();
                $this->code = trim($task->code);
            }
        }

        echo $now . PHP_EOL;
        return true;
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