<?php

namespace App\Console\Commands;

use App\Common\Koudai\KdUser;
use App\Common\Koudai\Shake;
use App\Common\Koudai\Spider;
use App\Models\UserInfo;
use Illuminate\Console\Command;
use App\Common\Helper;
use App\Common\Koudai\Earn;

class TestJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test job';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = $this->argument('id');

        $lock_name = Helper::filterSignature($this->signature) . " " . $id;

        if (!Helper::Lock($lock_name)) {
            $this->comment($lock_name . " script is exists.");
            return false;
        }

        try {
            $this->comment("{$lock_name} start.");

            $spider = new Spider(sprintf(Spider::ORDER_URL, $id));
            $spider->doJob();
            $spider->analyseOrder();
        } catch (\Exception $e) {
            $this->comment($e->getMessage());
        } finally {
            $this->comment("{$lock_name} end.");
            Helper::unlock($lock_name);
        }
    }

}
