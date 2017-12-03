<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/30
 * Time: 00:37
 */

namespace App\Common\Koudai;

use App\Common\Helper;

class Factory
{
    private $cmd;
    private $taskId;
    public function __construct($task_id)
    {
        $this->taskId = $task_id;
    }

    public function createCmd($task)
    {
        $method = "create" . ucfirst($this->taskId) . "Cmd";
        if (method_exists($this, $method)) {
            return $this->$method($task);
        }

        return false;
    }

    private function createDailyCmd($task) {
        $this->cmd = Helper::getBash() . " {$this->taskId} " . $task->user_key . Helper::getLogOutput($this->taskId);
        return true;
    }

    private function createShareCmd($task) {
        return $this->createDailyCmd($task);
    }

    //{id} {product_id} {time_point} {code} {prize_number}
    private function createExchangeCmd($task) {
        $params = $task->user_key . " " . $task->product_id . " " . $task->time_point .
                    " " . $task->prize_number . " " . $task->id . " " . $task->code;
        $this->cmd = Helper::getBash() . " {$this->taskId} " . $params . Helper::getLogOutput($this->taskId);
        return true;
    }

    //{id} {product_id} {time_point} {money} {is_kdb_pay} {voucher_id} {is_wait_sjk}
    private function createOrderCmd($task) {
        $params = $task->user_key . " " . $task->product_id . " " . $task->time_point .
            " " . $task->money . " " . $task->is_kdb_pay . " " . $task->voucher_id . " " . $task->is_wait_sjk . " " . $task->id;
        $this->cmd = Helper::getBash() . " {$this->taskId} " . $params . Helper::getLogOutput($this->taskId);
        return true;
    }

    public function runCmd()
    {
        if ($this->cmd) {
            exec($this->cmd);
        }

        return true;
    }

    public function getCmd() {
        return $this->cmd;
    }
}