<?php

namespace App\Console\Commands;

use App\Common\Koudai\KdUser;
use App\Common\Koudai\Share;
use App\Common\Koudai\Spider;
use App\Models\ShareRecord;
use App\Models\UserInfo;
use App\Common\Helper;

class ShareJob extends BaseJob
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'share {id}';

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
        $user_id = $this->argument('id');

        $lock_name = Helper::filterSignature($this->signature) . "_" . $user_id;

        if (!Helper::Lock($lock_name)) {
            $this->comment($lock_name . " script is exists.");
            return false;
        }

        try {
            $this->comment("{$lock_name} start.");

            $user = UserInfo::where('user_key', $user_id)->firstOrFail();

            $kd_user = new KdUser($user->user_name, $user->password);

            $kd_user->login();

            $spider = new Spider(Spider::BBS_URL);
            $spider->setUserAgent(Helper::getUserAgent());
            $spider->doJob();
            $spider->analyseBbs();
            $result = $spider->getAnalyseData();
            var_dump($result);
            
            if (is_array($result) && count($result) > 0) {
                $share = new Share($kd_user->getCookie());

                foreach ($result as $item) {
                    $id = $item['question_id'];

                    $record = ShareRecord::where(['user_key' => $user_id, 'question_id' => $id])->first();
                    if (isset($record->id)) {
                        continue;
                    }

                    // Create ShareRecord
                    ShareRecord::create([
                        'user_key' => $user_id,
                        'question_id' => $id,
                    ]);

                    $share->doJob($id);
                    $this->comment("share {$id} result: " . $share->getErrorMsg());
                }
            }

        } catch (\Exception $e) {
            $this->comment($e->getMessage());
        } finally {
            $this->comment("{$lock_name} end.");
            Helper::unlock($lock_name);
        }
    }

}
