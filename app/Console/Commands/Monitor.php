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

        $now  = time();

        try {
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
                    //if ($item->run_time >= $now) {
                    $factory = new Factory($item->id);
                    $factory->createCmd($item);
                    $factory->runCmd();
                    $this->comment($factory->getCmd() . " is start run.\n");
                    DB::table("tasks")->where('id', $item->id)->update(['status' => 1]);
                    //}
                }

                if ((time() - $now) > 300) {
                    $this->comment($this->signature . " script is run 5 minutes.\n");
                    Helper::unlock($this->signature);
                    return true;
                }
            }
        } catch (\Exception $e) {
            $this->comment($e->getMessage());
        } finally {
            $this->comment($this->signature . " script is run 5 minutes.\n");
            Helper::unlock($this->signature);
            return true;
        }

    }

}
