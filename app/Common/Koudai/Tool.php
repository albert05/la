<?php

/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/11/29
 * Time: 23:39
 */
namespace App\Common\Koudai;

use \Curl\Curl;

class Tool
{
    private $url;
    private $url_list = [
        0 => "https://deposit.koudailc.com/user-project/project-investing-list",
    ];

    public function __construct($cookie)
    {
        $this->cookie = $cookie;
    }

    public function setUrl($idx) {
        $this->url = $this->url_list[$idx];
    }

    public function doJob()
    {
        $params = [
        ];

        $this->curl = new Curl();
        $this->curl->setCookie('SESSIONID', $this->cookie);
        $this->curl->post($this->url, $params);

        return $this->jsonFormat($this->curl->response);
//        return json_encode($this->curl->response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    /** Json数据格式化
     * @param  Mixed  $data   数据
     * @param  String $indent 缩进字符，默认4个空格
     * @return JSON
     */
    public function jsonFormat($data, $indent=null){

        // 对数组中每个元素递归进行urlencode操作，保护中文字符
        //array_walk_recursive($data, 'jsonFormatProtect');

        // json encode
        $data = json_encode($data);

        // 将urlencode的内容进行urldecode
        $data = urldecode($data);

        // 缩进处理
        $ret = '';
        $pos = 0;
        $length = strlen($data);
        $indent = isset($indent)? $indent : '    ';
        $newline = "\n</br>";
        $prevchar = '';
        $outofquotes = true;

        for($i=0; $i<=$length; $i++){

            $char = substr($data, $i, 1);

            if($char=='"' && $prevchar!='\\'){
                $outofquotes = !$outofquotes;
            }elseif(($char=='}' || $char==']') && $outofquotes){
                $ret .= $newline;
                $pos --;
                for($j=0; $j<$pos; $j++){
                    $ret .= $indent;
                }
            }

            $ret .= $char;

            if(($char==',' || $char=='{' || $char=='[') && $outofquotes){
                $ret .= $newline;
                if($char=='{' || $char=='['){
                    $pos ++;
                }

                for($j=0; $j<$pos; $j++){
                    $ret .= $indent;
                }
            }

            $prevchar = $char;
        }

        return $ret;
    }

    /** 将数组元素进行urlencode
     * @param String $val
     */
    public function jsonFormatProtect(&$val){
        if($val!==true && $val!==false && $val!==null){
            $val = urlencode($val);
        }
    }

}