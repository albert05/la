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
    private $is_wait_sjk = 0;

    public function __construct($cookie)
    {
        parent::__construct(self::EXCHANGE_URL);
        $this->cookie = $cookie;
    }

    public function doJob($task_id = '')
    {
        $this->waitIt($task_id);

        if ($this->is_wait_sjk) {
            $spider = new Spider(Spider::EXCHANGE_MONITOR_URL);
            $spider->waitExchange();
        }

        $params = [
            'id' => $this->product_id,
            'imgcode' => $this->code,
            'prize_number' => $this->prize_number,
        ];

        echo "start_tme: " . Helper::getMicrotime() . "\n";

        $this->curl->setCookie('SESSIONID', $this->cookie);
        $this->curl->post($this->url, $params);

        echo "end_tme: " . Helper::getMicrotime() . "\n";

        return $this->setResult($this->curl->response);
    }

    protected function waitIt($task_id) {
        if (!$this->time_point) {
            return true;
        }

        $now = Helper::getMicrotime();

        while ($now < $this->time_point) {
            usleep(10); // 0.01毫秒
            if (!$this->code) {
                $task = Task::where(['id' => $task_id])->first();
                $this->code = trim($task->code);
            }
            $now = Helper::getMicrotime();
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

    public function setIsWaitSjk($is_wait_sjk) {
        $this->is_wait_sjk = $is_wait_sjk;
    }
}