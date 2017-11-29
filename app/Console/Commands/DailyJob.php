<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use App\Common\Helper;

class DailyJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily';

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

        var_dump($this->argument());


        $this->comment($this->signature . " script is run 5 minutes.\n");
        Helper::unlock($this->signature);

    }

}
