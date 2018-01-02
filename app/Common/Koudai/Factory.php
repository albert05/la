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
    private $workId;
    public function __construct($work_id)
    {
        $this->workId = $work_id;
    }

    public function createCmd($task)
    {
        $this->cmd = Helper::getBash() . $this->workId . " " . $task->id . Helper::getLogOutput($this->workId, $task->user_key);
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