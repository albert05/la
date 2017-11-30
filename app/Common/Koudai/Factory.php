<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/30
 * Time: 00:37
 */

namespace App\Common\Koudai;

use App\Common\Helper;
use App\Models\Task;

class Factory
{
    private $cmd;
    private $taskId;
    public function __construct($task_id)
    {
        $this->taskId = $task_id;
    }

    public function createCmd(Task $task)
    {
        $method = "create" . ucfirst($this->taskId) . "Cmd";
        if (method_exists(self, $method)) {
            return $this->$method($task);
        }

        return false;
    }

    private function createDailyCmd(Task $task) {
        $this->cmd = Helper::getBash() . " {$this->taskId} " . $task->user_key . Helper::getLogOutput($this->taskId);
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