<?php

namespace App\Console\Commands;

use App\Common\Koudai\KdUser;
use App\Common\Koudai\Spider;
use App\Models\UserInfo;
use Illuminate\Console\Command;
use App\Common\Helper;

class ShareJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'share';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'share job';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        $user_id = $this->argument('id');

        $lock_name = Helper::filterSignature($this->signature);//. " " . $user_id;

        if (!Helper::Lock($lock_name)) {
            $this->comment($lock_name . " script is exists.");
            return false;
        }

        try {
            $this->comment("{$lock_name} start.");

//            $user = UserInfo::where('user_key', $user_id)->firstOrFail();
//
//            $kd_user = new KdUser($user->user_name, $user->password);
//
//            $kd_user->login();

            $spider = new Spider();
            $spider->doJob();
            $spider->analyse();
//            $this->comment("spider result: " . $earn->getErrorMsg());

//            $shake = new Shake($kd_user->getCookie());
//            $shake->doJob();
//            $this->comment("shake result: " . $shake->getErrorMsg());
        } catch (\Exception $e) {
            $this->comment($e->getMessage());
        } finally {
            $this->comment("{$lock_name} end.");
            Helper::unlock($lock_name);
        }
    }

}
