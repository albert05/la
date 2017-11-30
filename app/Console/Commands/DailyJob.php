<?php

namespace App\Console\Commands;

use App\Common\Koudai\KdUser;
use App\Models\UserInfo;
use Illuminate\Console\Command;
use App\Common\Helper;
use App\Common\Koudai\Earn;

class DailyJob extends Command
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
        if (!Helper::Lock($this->signature)) {
            $this->comment($this->signature . " script is exists.\n");
            return false;
        }

        try {
            $user_id = $this->argument('id');

            $user = UserInfo::where('user_key', $user_id)->get();

            var_dump($user);

            $kd_user = new KdUser($user->user_name, $user->password);

            $kd_user->login();

            $earn = new Earn($kd_user->getCookie());
            $earn->doJob();
            var_dump($earn->getErrorMsg());
        } catch (\Exception $e) {
            $this->comment($e->getMessage());
        } finally {
            $this->comment($this->signature . " script is run end.\n");
            Helper::unlock($this->signature);
        }
    }

}
