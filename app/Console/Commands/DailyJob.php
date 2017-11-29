<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use App\Common\Helper;
use App\Common\Koudai\User;
use App\Common\Koudai\Earn;

class DailyJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily {username} {password}';

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

        $username = $this->argument('username');
        $password = $this->argument('password');

        $user = new User($username, $password);

        $user->login();

        $earn = new Earn($user->getCookie());
        $earn->run();
        var_dump($earn->getErrorMsg());

        $this->comment($this->signature . " script is run end.\n");
        Helper::unlock($this->signature);

    }

}
