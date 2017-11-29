<?php

namespace App\Console\Commands;

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

        $now  = time();

        while (true) {
            $task_list = DB::table('tasks')
                ->where('status', 0)
                ->get();

            if (!$task_list) {
                $this->comment("task list is none");
                sleep(5);
            }

            $now = intval(date('gis', time()));
            foreach ($task_list as $item) {
                //if ($item['run_time'] >= $now) {
                    $this->runCmd($item['run_time']);
                    $this->comment($item['run_time'] . " is start run.\n");
                    DB::table("tasks")->where('id', $item['id'])->update(['status' => 1]);
                //}
            }

            if ((time() - $now) > 300) {
                $this->comment($this->signature . " script is run 5 minutes.\n");
                Helper::unlock($this->signature);
                return true;
            }
        }

    }

    private function runCmd($cmd) {
        exec($cmd);
    }
}
