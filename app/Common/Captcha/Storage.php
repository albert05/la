<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 2017/12/3
 * Time: 14:47
 */

namespace App\Common\Captcha;


use App\Models\Captcha;

class Storage
{
    /**
     * @var
     */
    protected $_db;

    public function __construct()
    {
        $this->connect();
    }

    /**
     * 初始化连接
     * @throws \Exception
     */
    public function connect(){
        $captchas = Captcha::all();
        if (!$captchas) {
            $this->_db = [];
        }

        foreach ($captchas as $item) {
            $this->_db[$item->code][] = $item->hash;
        }
    }

    /**
     * 添加数据
     * @param $code
     * @param $hash_data
     */
    public function add($code,$hash_data){
        $this->_db[$code][]=$hash_data;
        $this->save();
    }

    /**
     * 保存数据
     * @throws \Exception
     */
    public function save(){
        if (count($this->_db) > 0) {

        }
        foreach ($this->_db as $code => $v) {
            foreach ($v as $hash) {
                $c = Captcha::where(['code' => $code, 'hash' => $hash])->first();
                if (!$c) {
                    Captcha::create([
                        'code' => $code,
                        'hash' => $hash,
                    ]);
                }
            }
        }
    }

    /**
     * 获取所有数据
     * @return mixed
     */
    public function get(){
        return $this->_db;
    }
}