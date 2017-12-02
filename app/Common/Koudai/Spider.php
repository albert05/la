<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

use Symfony\Component\DomCrawler\Crawler;

class Spider extends Base
{
//    const BBS_URL = "https://bbs.koudailc.com/m";
//    const BBS_URL = "https://bbs.koudailc.com/category/54";
    const BBS_URL = "https://bbs.koudailc.com/?/m/ajax/elite_list/sort_type-new__day-0__is_recommend-0_page-1";
    private $response;

    public function __construct()
    {
        parent::__construct(self::BBS_URL);
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


    public function analyseBbs() {
        //进行XPath页面数据抽取
        $data    = []; //结构化数据存本数组
        $crawler = new Crawler();
        $crawler->addHtmlContent($this->response);

        try {

//            $data['name'] = $crawler->filterXPath('//*[@id="cg_content"]/div[contains(@class,"cg_content_left")]/div[contains(@class,"cg_down")]/div[1]/div')->text();
            $list = $crawler->filterXPath('//a')->each();

            $count = count($list);
            for ($i = 0; $i < $count; $i++) {
                $data[] = $crawler->filterXPath("//a[" . $i . "][@href]")->text();
            }


            var_dump($data);

//            //电影名称
//            //网页结构中用css选择器用id的比较容易写xpath表达式
//            $data['name'] = $crawler->filterXPath('//*[@id="content"]/h1/span[1]')->text();
//            //电影海报
//            $data['cover'] = $crawler->filterXPath('//*[@id="mainpic"]/a/img/@src')->text();
//            //导演
//            $data['director'] = $crawler->filterXPath('//*[@id="info"]/span[1]/span[2]')->text();
//            //多个导演处理成数组
//            $data['director'] = explode('/', $data['director']);
//            //过滤前后空格
//            $data['director'] = array_map('trim', $data['director']);
//
//            //编剧
//            $data['cover'] = $crawler->filterXPath('//*[@id="info"]/span[2]/span[2]/a')->text();
//            //主演
//            $data['mactor'] = $crawler->filterXPath('//*[@id="info"]/span[contains(@class,"cg_content_left")]/span[contains(@class,"attrs")]')->text();
//            //多个主演处理成数组
//            $data['mactor'] = explode('/', $data['mactor']);
//            //过滤前后空格
//            $data['mactor'] = array_map('trim', $data['mactor']);
//
//            //上映日期
//            $data['rdate'] = $crawler->filterXPath('//*[@id="info"]')->text();
//            //使用正则进行抽取
//            preg_match_all("/(\d{4})-(\d{2})-(\d{2})\(.*?\)/", $data['rdate'], $rdate); //2017-07-07(中国大陆) / 2017-06-14(安锡动画电影节) / 2017-06-30(美国)
//            $data['rdate'] = $rdate[0];
//            //简介
//            //演示使用class选择器的方式
//            $data['introduction'] = trim($crawler->filterXPath('//div[contains(@class,"indent")]/span')->text());
//
//            //演员
//            //本xpath表达式会得到多个对象结果,用each方法进行遍历
//            //each是传入的参数是一个闭包,在闭包中使用外部的变量使用use方法,并使用变量指针
//            $crawler->filterXPath('//ul[contains(@class,"celebrities-list from-subject")]/li')->each(function (Crawler $node, $i) use (&$data) {
//                $actor['name']   = $node->filterXPath('//div[contains(@class,"info")]/span[contains(@class,"name")]/a')->text(); //名字
//                $actor['role']   = $node->filterXPath('//div[contains(@class,"info")]/span[contains(@class,"role")]')->text(); //角色
//                $actor['avatar'] = $node->filterXPath('//a/div[contains(@class,"avatar")]/@style')->text(); //头像
//                //background-image: url(https://img3.doubanio.com/img/celebrity/medium/5253.jpg) 正则抽取头像图片
//                preg_match_all("/((https|http|ftp|rtsp|mms)?:\/\/)[^\s]+\.(jpg|jpeg|gif|png)/", $actor['avatar'], $avatar);
//                $actor['avatar'] = $avatar[0][0];
//                //print_r($actor);
//                $data['actor'][] = $actor;
//            });

        } catch (\Exception $e) {

        }

        return $data;

    }

}