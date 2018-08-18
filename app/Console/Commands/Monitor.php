<?php

namespace App\Console\Commands;

use App\Common\Koudai\Factory;
use DB;
use App\Common\Helper;

class Monitor extends BaseJob
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'monitor task';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!Helper::Lock($this->signature)) {
            $this->comment($this->signature . " script is exists.\n");
            return false;
        }

        $t_now  = time();

        try {
            while (true) {
                $task_list = DB::table('tasks')
                    ->where('status', 0)
                    ->get();

                if (!$task_list) {
                    $this->comment("task list is none");
                    sleep(5);
                }

                $now = date('Y-m-d H:i:s', time());
                foreach ($task_list as $item) {
                    if (in_array($item->work_id, ["exchange", "abcGift"])) {
                        continue;
                    }
                    if ($item->run_time <= $now) {
                        $factory = new Factory($item->work_id);
                        $factory->createCmd($item);
                        $factory->runCmd();
                        $this->comment($factory->getCmd() . " is start run.\n");
                        $this->updateStatus($item->id, $item->work_id, $item->run_time);
                    }
                }

                unset($task_list);
                if ((time() - $t_now) > 1200) {
                    $this->comment($this->signature . " script is run 20 minutes.\n");
                    Helper::unlock($this->signature);
                    return true;
                }

                $this->comment("task sleep 5 seconds.");
                sleep(5);
            }
        } catch (\Exception $e) {
            $this->comment($e->getMessage());
        } finally {
            Helper::unlock($this->signature);
            return true;
        }

    }

    private function updateStatus($id, $work_id, $time) {
        if ($work_id == 'daily') {
            DB::table("tasks")->where('id', $id)->update(['run_time' => date("Y-m-d H:i:s", strtotime($time) + 86400)]);
        } elseif ($work_id == 'share') {
            DB::table("tasks")->where('id', $id)->update(['run_time' => $this->handleShareTime($time)]);
        } else if ($work_id == 'transfer') {
            DB::table("tasks")->where('id', $id)->update(['run_time' => $this->handleTransferTime($time)]);
        } else {
            DB::table("tasks")->where('id', $id)->update(['status' => 1]);
        }

        return true;
    }

    private function handleShareTime($time) {
        $time = max(date("Y-m-d H:30:s", time()), date("Y-m-d H:30:s", strtotime($time)));

        $hour = intval(date("H", strtotime($time)));

        if ($hour <= 17) {
            $date = date("Y-m-d 17:30:00", time());
        } else {
            $date = date("Y-m-d H:i:s", strtotime($time) + 7200);
        }

        return $date;
    }

    private function handleTransferTime($time) {
        $time = date("Y-m-d H:i:s", max(time(), strtotime($time)));

        $hour = intval(date("H", strtotime($time)));

        $n = rand(0, 40);
        $m = $n >= 10 ? $n : "0{$n}";

        if ($hour >= 1 && $hour <= 5) {
            $date = date("Y-m-d 06:{$m}:00");
        } else {
            $date = date("Y-m-d H:i:s", strtotime($time) + 3600);
        }

        return $date;
    }

}
