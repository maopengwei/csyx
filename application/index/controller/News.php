<?php

namespace app\index\controller;

use think\Route;
use app\common\controller\Api;

/*
    新闻
 */
class News extends Api 
{


    public function index() {
        $this->map[] = ['me_type','=',1];
        $list = model('Message')->where($this->map)->order($this->order)->paginate($this->size);
        $this->msg($list);
    }

    // 详情
    public function xq() {
        if(input('id')){
            $info = model("Message")->where('id',input('id'))->find();
        }else{
            $info = model('Message')->where('me_type',1)->order('id desc')->field('id,me_introduction')->find();
        }
        $this->msg($info);
    }
    
}