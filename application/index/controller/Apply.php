<?php

namespace app\index\controller;

use think\Request;
use think\Db;
/**
 *  商店
 */
class Apply extends Base
{

    // public function initialize(){
    //     parent::initialize();
    //     $this->user = model('User')->where("us_tel",13000000000)->find();
    // }
    
    //申请成为门店
    public function apply()
    {
        if(is_post()){
            $d = input('post.');

            $validate = validate('Apply');
            $res = $validate->scene('apply')->check($d);
            if (!$res) {
                $this->e_msg($validate->getError());
            }
            $arr = [
                'us_id' => $this->user['id'],
                'apply_card_front' => $d['apply_card_front'],  //身份证前
                'apply_card_back' => $d['apply_card_back'], //身份证后
                'apply_trad' => $d['apply_trad'],           //营业执照
                'apply_ying' => $d['apply_ying'],           //手持身份证照
                'apply_name' => $d['apply_name'],           //店铺名称
                'apply_tel' => $d['apply_tel'],             //手机号
                'apply_person' => $d['apply_person'],       //人
                'apply_addr' => $d['apply_addr'],           //地址
                'apply_note' => $d['apply_note'],           //说明
                'apply_add_time' => date('Y-m-d H:i:s'),
            ];
            $rel = db('sto_apply')->insert($arr);
            if($rel){
                $this->s_msg('申请成功');
            }else{
                $this->e_msg('申请失败');
            }
        }
    }
    /*
        判断是否已申请
    */
    public function is_apply(){
        $info = db('sto_apply')->where('us_id',$this->user['id'])->find();
        if($info){
            $arr = [
                'data' => $info,
                'code' => 1, 
            ];
            $this->msg($arr);
        }else{
            $this->e_msg('没有');
        }
    }
}
    