<?php

namespace App\Console\Commands;

use App\Common\Koudai\Exchange;
use Illuminate\Console\Command;
use App\Common\Helper;

class ExchangeJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange {id} {product_id} {time_point} {code} {prize_number}';

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

        $lock_name = Helper::filterSignature($this->signature) . " " . $user_id;

        if (!Helper::Lock($lock_name)) {
            $this->comment($lock_name . " script is exists.");
            return false;
        }

        try {
            $this->comment("{$lock_name} start.");

            $cookie = $user_id;

            $exchange = new Exchange($cookie);
            $exchange->setProductId($product_id);
            $exchange->setCode($code);
            $exchange->setPrizeNumber($prize_number);
            $exchange->setTimePoint($time_point);
            $exchange->doJob();
            $this->comment("exchange result: " . $exchange->getErrorMsg());

        } catch (\Exception $e) {
            $this->comment($e->getMessage());
        } finally {
            $this->comment("{$lock_name} end.");
            Helper::unlock($lock_name);
        }
    }

}
