<?php

namespace App\Console\Commands;

use App\Common\Koudai\KdUser;
use App\Common\Koudai\Shake;
use App\Common\Koudai\Spider;
use App\Models\UserInfo;
use App\Common\Helper;
use App\Common\Koudai\Earn;
use Illuminate\Support\Facades\Redis;

class TestJob extends BaseJob
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
        ;

        $lock_name = Helper::filterSignature($this->signature) . " " . $id;

        if (!Helper::Lock($lock_name)) {
            $this->comment($lock_name . " script is exists.");
            return false;
        }

        try {
            $this->comment("{$lock_name} start.");

            Redis::set("test", 'albert');
            echo Redis::get('test');

//            $image=new \Mohuishou\ImageOCR\Image(asset('la-assets/img/captcha/captcha.png'));
//
//            $image->draw();
//
//            var_dump($image->find());


//            $spider = new Spider(sprintf(Spider::ORDER_URL, $id));
//            $spider->doJob();
//            $spider->analyseOrder();
        } catch (\Exception $e) {
            $this->comment($e->getMessage());
        } finally {
            $this->comment("{$lock_name} end.");
            Helper::unlock($lock_name);
        }
    }

}
