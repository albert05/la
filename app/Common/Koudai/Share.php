<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

class Share extends Base
{
    const SHARE_URL = "https://deposit.koudailc.com/activity/success-up";
    const ACTIVITY_URL = 'https://deposit.koudailc.com/page/koudai-bbs?redirectUrl=https://bbs.koudailc.com&clientType=android&appVersion=6.4.0&deviceName=1505-A02&appMarket=360_sc&isShare=1';

    public function __construct($cookie)
    {
        parent::__construct(self::SHARE_URL);
        $this->cookie = $cookie;
    }


    public function doJob($activity_id)
    {
        $params = [
            'share_type' => 3,
            'activity_url' => self::ACTIVITY_URL,
            'share_platform' => 1,
            'activity_id' => $activity_id,
        ];
        return $this->run($params);
    }

}