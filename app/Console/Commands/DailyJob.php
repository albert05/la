<?php

namespace App\Console\Commands;

use App\Common\Koudai\KdUser;
use App\Common\Koudai\Shake;
use App\Models\Task;
use App\Models\UserInfo;
use App\Common\Helper;
use App\Common\Koudai\Earn;

class DailyJob extends BaseJob
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'daily job';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $task_id = $this->argument('id');


        $lock_name = Helper::filterSignature($this->signature) . "_" . $task_id;

        if (!Helper::Lock($lock_name)) {
            $this->comment($lock_name . " script is exists.");
            return false;
        }

        try {
            $this->comment("{$lock_name} start.");

            $task = Task::where('id', $task_id)->firstOrFail();
            $user = UserInfo::where('user_key', $task->user_key)->firstOrFail();

            $kd_user = new KdUser($user->user_name, $user->password);

            $kd_user->login();

            $earn = new Earn($kd_user->getCookie());
            $earn->doJob();
            $this->comment("earn result: " . $earn->getErrorMsg());

            $shake = new Shake($kd_user->getCookie());
            $shake->doJob();
            $this->comment("shake result: " . $shake->getErrorMsg());
        } catch (\Exception $e) {
            $this->comment($e->getMessage());
        } finally {
            $this->comment("{$lock_name} end.");
            Helper::unlock($lock_name);
        }
    }

}
