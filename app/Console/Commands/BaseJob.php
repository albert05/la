<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BaseJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    public function __construct()
    {
        parent::__construct();
        error_reporting(E_ERROR);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    }

}
