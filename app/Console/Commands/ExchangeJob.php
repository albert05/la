<?php

namespace App\Console\Commands;

use App\Common\Koudai\Code;
use App\Common\Koudai\Exchange;
use App\Common\Koudai\KdUser;
use App\Common\Login;
use App\Models\Task;
use App\Models\UserInfo;
use App\Common\Helper;

class ExchangeJob extends BaseJob
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'exchange job';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $task_id = $this->argument('id');
        $task = Task::where('id', $task_id)->firstOrFail();
        $user_id = $task->user_key;
        $code = $task->code;
        $product_id = $task->product_id;
        $prize_number = $task->prize_number;
        $time_point = $task->time_point;
        $is_wait_sjk = $task->is_wait_sjk;

        $lock_name = Helper::filterSignature($this->signature) . "_" . $task_id;

        if (!Helper::Lock($lock_name)) {
            $this->comment($lock_name . " script is exists.");
            return false;
        }

        try {
            $this->comment("{$lock_name} start.");

            $cookie = $user_id;
            if (!$code) {
                $login = new Login();
                $info = $login->kd_login($user_id);

                $xcode = new Code($info['cookie']);
                if (!$info['is_cache']) {
                    $xcode->refresh();
                }
                $xcode->doJob();
                $filename = $xcode->getFileName();
                Task::where('id', $task_id)->update([
                    'img_url' => $filename,
                ]);
            }

            $exchange = new Exchange($cookie);
            $exchange->setProductId($product_id);
            $exchange->setCode($code);
            $exchange->setPrizeNumber($prize_number);
            $exchange->setTimePoint($time_point);
            $exchange->setIsWaitSjk($is_wait_sjk);
            $exchange->doJob($task_id);
            $this->comment("exchange result: " . $exchange->getErrorMsg());

            Task::where('id', $task_id)->update([
                'result' => $exchange->getErrorMsg(),
                'status' => $exchange->getErrorNo() == 0 ? 3 : 2,
            ]);

        } catch (\Exception $e) {
            $this->comment($e->getMessage() . $e->getTraceAsString() . $e->getFile() . $e->getLine());
        } finally {
            $this->comment("{$lock_name} end.");
            Helper::unlock($lock_name);
        }
    }



}
