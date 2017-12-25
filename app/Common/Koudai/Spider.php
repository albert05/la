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
    private $response;
    private $analyse_data = [];

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
        $crawler = new Crawler();
        $crawler->addHtmlContent($this->response);

        try {
//            $crawler->filterXPath('//div[contains(@class,"koudai_sjk")]')->text();
            $text = $crawler->filterXPath('//div[contains(@class,"table-detail")]/table/tbody/tr/td')->text();
            var_dump($text);

            return $text != "暂无数据";
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function waitOrder() {
        $this->doJob();
        while(!$this->analyseOrder()) {
            usleep(10);
            $this->doJob();
        }
    }

}