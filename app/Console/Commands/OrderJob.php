<?php

namespace App\Console\Commands;

use App\Common\Koudai\KdUser;
use App\Common\Koudai\Order;
use App\Models\Task;
use App\Models\UserInfo;
use App\Common\Helper;

class OrderJob extends BaseJob
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'order job';

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
        $money = $task->money;
        $product_id = $task->product_id;
        $is_kdb_pay = $task->is_kdb_pay;
        $time_point = $task->time_point;
        $voucher_ids = $task->voucher_id;
        $is_wait_sjk = $task->is_wait_sjk;
        $order_number = $task->order_number;

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

            $order = new Order($kd_user->getCookie(), $user->pay_passwd);
            $order->setProductId($product_id);
            $order->setMoney($money);
            $order->setIsKdbPay($is_kdb_pay);
            $order->setTimePoint($time_point);
            $order->setIsWaitSjk($is_wait_sjk);

            $voucher_id_list = explode(":", $voucher_ids);

            $failed_count = 0;
            $succ_count = 0;
            for ($i = 0; $i < $order_number; $i++) {
                if (isset($voucher_id_list[$succ_count])) {
                    $order->setVoucherId($voucher_id_list[$succ_count]);
                } else {
                    $order->setVoucherId(0);
                }
                $order->doJob();
                if ($order->getErrorNo() != 0) {
                    $failed_count++;
                    $order_number++;
                } else {
                    $succ_count++;
                }

                $this->comment("order result: " . $order->getErrorMsg());
                $this->comment("order detail: " . $order->getDetail());

                // 失败30次或20秒退出
                if ($failed_count > 50 || intval(date('s')) > 20 ) {
                    break;
                }
            }

            Task::where('id', $task_id)->update([
                'result' => $order->getErrorMsg(),
                'detail' => $order->getDetail(),
                'status' => $order->getErrorNo() == 0 ? 3 : 2,
            ]);

        } catch (\Exception $e) {
            $this->comment($e->getMessage());
        } finally {
            $this->comment("{$lock_name} end.");
            Helper::unlock($lock_name);
        }
    }

}
