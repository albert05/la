<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

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
        $task_list = DB::table('tasks')
            ->where('status', 0)
            ->get();

        if (!$task_list) {
            $this->comment();
            sleep(5);
        }

        $now = intval(date('gis', time()));
        foreach ($task_list as $item) {
            if ($item['run_time'] >= $now) {
                $this->runCmd($item['run_time']);
                DB::table("tasks")->where('id', $item['id'])->update(['status' => 1]);
            }
        }

    }

    private function runCmd($cmd) {
        exec($cmd);
    }
}
