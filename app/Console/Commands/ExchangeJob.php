<?php

namespace App\Console\Commands;

use App\Common\Koudai\Code;
use App\Common\Koudai\Exchange;
use App\Common\Koudai\KdUser;
use App\Models\Task;
use App\Models\UserInfo;
use Illuminate\Console\Command;
use App\Common\Helper;

class ExchangeJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange {id} {product_id} {time_point} {prize_number} {task_id} {code?}';

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
        $user_id = $this->argument('id');
        $code = $this->argument('code');
        $product_id = $this->argument('product_id');
        $prize_number = $this->argument('prize_number');
        $time_point = $this->argument('time_point');
        $task_id = $this->argument('task_id');

        $lock_name = Helper::filterSignature($this->signature) . " " . $user_id;

        if (!Helper::Lock($lock_name)) {
            $this->comment($lock_name . " script is exists.");
            return false;
        }

        try {
            $this->comment("{$lock_name} start.");

            $cookie = $user_id;
            if (!$code) {
                $user = UserInfo::where('user_key', $user_id)->firstOrFail();

                $kd_user = new KdUser($user->user_name, $user->password);

                $kd_user->login();
                $cookie = $kd_user->getCookie();

                $code = new Code($cookie);
                $code->refresh();
                $code->doJob();
                $filename = $code->getFileName();
                Task::where('id', $task_id)->update([
                    'img_url' => $filename,
                ]);
            }

            $exchange = new Exchange($cookie);
            $exchange->setProductId($product_id);
            $exchange->setCode($code);
            $exchange->setPrizeNumber($prize_number);
            $exchange->setTimePoint($time_point);
            $exchange->doJob($task_id);
            $this->comment("exchange result: " . $exchange->getErrorMsg());

            Task::where('id', $task_id)->update([
                'result' => $exchange->getErrorMsg(),
                'status' => $exchange->getErrorNo() == 0 ? 3 : 2,
            ]);

        } catch (\Exception $e) {
            $this->comment($e->getMessage() . $e->getTrace());
        } finally {
            $this->comment("{$lock_name} end.");
            Helper::unlock($lock_name);
        }
    }

}
