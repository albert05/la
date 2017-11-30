<?php

namespace App\Console\Commands;

use App\Common\Koudai\Exchange;
use App\Common\Koudai\KdUser;
use App\Common\Koudai\Order;
use App\Models\UserInfo;
use Illuminate\Console\Command;
use App\Common\Helper;

class OrderJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order {id} {product_id} {time_point} {money} {is_kdb_pay} {voucher_id}';

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
        $user_id = $this->argument('id');
        $money = $this->argument('money');
        $product_id = $this->argument('product_id');
        $is_kdb_pay = $this->argument('is_kdb_pay');
        $time_point = $this->argument('time_point');
        $voucher_id = $this->argument('voucher_id');

        $lock_name = Helper::filterSignature($this->signature) . " " . $user_id;

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
            $order->setVoucherId($voucher_id);
            $order->doJob();
            $this->comment("order result: " . $order->getErrorMsg());

        } catch (\Exception $e) {
            $this->comment($e->getMessage());
        } finally {
            $this->comment("{$lock_name} end.");
            Helper::unlock($lock_name);
        }
    }

}
