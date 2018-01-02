<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

use App\Common\Helper;
use Symfony\Component\DomCrawler\Crawler;

class Spider extends Base
{
//    const BBS_URL = "https://bbs.koudailc.com/m";
//    const BBS_URL = "https://bbs.koudailc.com/category/54";
    const BBS_URL = "https://bbs.koudailc.com/?/m/ajax/elite_list/sort_type-new__day-0__is_recommend-0_page-1";
    const ORDER_URL = "https://www.koudailc.com/list/detail?id=%s&channelId=4";
    const ORDER_RECORD_URL = "https://deposit.koudailc.com/project/invest-list?clientType=pc&id=%s";
    const EXCHANGE_MONITOR_URL = "https://deposit.koudailc.com/intergration/home-new?page=1&pageSize=8";
    private $response;
    private $analyse_data = [];
    private $voucher_total = [
        1 => 5,
        2 => 10,
        3 => 5,
        4 => 3,
    ];

    public function __construct($url)
    {
        parent::__construct($url);
    }

    public function doJob()
    {
        $this->curl->setUserAgent($this->user_agent);
        $this->curl->get($this->url);

        $this->response = $this->curl->response;
    }

    public function getResponse() {
        return $this->response;
    }

    public function getAnalyseData() {
        return $this->analyse_data;
    }


    public function analyseBbs() {
        $crawler = new Crawler();
        $crawler->addHtmlContent($this->response);

        try {
            $count = $crawler->filterXPath('//a')->count();

            for ($i = 1; $i <= $count; $i++) {
                $url = $crawler->filterXPath("//a[" . $i . "]")->attr("href");
                $params = Helper::analyzeUrl($url);
                if (isset($params['is_up']) && isset($params['question_id']) && $params['is_up'] == 1) {
                    $this->analyse_data[]['question_id'] = trim($params['question_id']);
                }
            }

        } catch (\Exception $e) {
            echo "{$e->getMessage()}\n";
        }
    }

    public function analyseOrder() {
        return $this->response->code == 0 && count($this->response->invests) > 0;

//        $crawler = new Crawler();
//        $crawler->addHtmlContent($this->response);
//
//        try {
//            $crawler->filterXPath('//div[contains(@class,"koudai_sjk")]')->text();
//        } catch (\Exception $e) {
//            return false;
//        }
//
//        return true;
    }

    public function waitOrder() {
        $this->doJob();
        while(!$this->analyseOrder()) {
//            usleep(10);
            $this->doJob();
        }
    }


    public function analyseExchange() {
        foreach ($this->response->prize_channel[0]->prize_info as $key => $value) {
            if ($this->voucher_total[$key] > $value) {
                return true;
            }
        }

        return false;
    }

    public function waitExchange() {
        $this->doJob();
        while(!$this->analyseExchange()) {
            usleep(10);
            $this->doJob();
        }
    }

}