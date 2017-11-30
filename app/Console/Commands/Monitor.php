<?php

namespace App\Console\Commands;

use App\Common\Koudai\Factory;
use DB;
use Illuminate\Console\Command;
use App\Common\Helper;

class Monitor extends Command
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
                    if ($item->run_time <= $now) {
                        $factory = new Factory($item->work_id);
                        $factory->createCmd($item);
                        $factory->runCmd();
                        $this->comment($factory->getCmd() . " is start run.\n");
                        $this->updateStatus($item->id, $item->work_id, $item->run_time);
                    }
                }

                if ((time() - $t_now) > 300) {
                    $this->comment($this->signature . " script is run 5 minutes.\n");
                    Helper::unlock($this->signature);
                    return true;
                }
            }
        } catch (\Exception $e) {
            $this->comment($e->getMessage());
        } finally {
            Helper::unlock($this->signature);
            return true;
        }

    }

    private function updateStatus($id, $work_id, $time) {
        if (in_array($work_id, ['daily'])) {
            DB::table("tasks")->where('id', $id)->update(['run_time' => date("Y-m-d H:i:s", strtotime($time) + 86400)]);
        } else {
            DB::table("tasks")->where('id', $id)->update(['status' => 1]);
        }
    }

}
