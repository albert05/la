<?php

namespace App\Console\Commands;

use App\Common\Koudai\Assign;
use App\Common\Koudai\KdUser;
use App\Models\Task;
use App\Models\UserInfo;
use App\Common\Helper;

class TransferJob extends BaseJob
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer {id} {product_id} {money} {task_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'transfer job';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user_id = $this->argument('id');
        $product_id = $this->argument('product_id');
        $money = $this->argument('money');
        $task_id = $this->argument('task_id');

        $lock_name = Helper::filterSignature($this->signature) . "_" . $task_id;

        if (!Helper::Lock($lock_name)) {
            $this->comment($lock_name . " script is exists.");
            return false;
        }

        try {
            $this->comment("{$lock_name} start.");

            $user = UserInfo::where('user_key', $user_id)->firstOrFail();

            $kd_user = new KdUser($user->user_name, $user->password);

            $kd_user->login();

            $assign = new Assign($kd_user->getCookie(), $user->pay_passwd);
            $assign->setMoney($money);
            $assign->setProductId($product_id);
            $assign->analyseList();
            $assign->cancel();
            $assign->doJob();
            $this->comment("assign result: " . $assign->getErrorMsg());

            if ($assign->isAssign()) {
                Task::where('id', $task_id)->update([
                    'result' => $assign->getErrorMsg(),
                    'status' => 3,
                ]);
            }

        } catch (\Exception $e) {
            $this->comment($e->getMessage());
        } finally {
            $this->comment("{$lock_name} end.");
            Helper::unlock($lock_name);
        }
    }

}
