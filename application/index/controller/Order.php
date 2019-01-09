<?php

namespace app\index\controller;
use think\Db;
use think\Request;

/**
 * 商城
 */
class Order extends Base
{


    
    public function zhun(){

        /*
            {
            arr:{
    
                0=> [
                  ["mer_id"] => string(1) "2"
                  ["arr"] => array(2) {
                    [0] => array(4) {
                      ["prod_id"] => string(2) "25"
                      ["num"] => string(1) "1"
                      ["prod_price_yuan"] => string(6) "800.00"
                      ["prod_price"] => string(6) "200.00"
                    }
                    [1] => array(4) {
                      ["prod_id"] => string(2) "26"
                      ["num"] => string(1) "2"
                      ["prod_price_yuan"] => string(7) "1200.00"
                      ["prod_price"] => string(6) "600.00"
                    }
                  }
                ]
                1=> [



                    .]

                }
            type:0|1
            }

        */

        if(is_post()){
            $d = input('post.');

            $content = serialize(input('arr'));
            // $content = unserialize($content);
            // halt($content);
            $brr = [

                 'us_id' => $this->user['id'],
                 'content' => $content,
                 'type' =>  $d['type'],
                 'add_time' => date('Y-m-d H:i:s'),
            ];

            $id = Db::name('zhun')->insertGetId($brr);
            if($id){
                $arr = [
                    'id' => $id,
                    'code' => 1,
                ];
                $this->msg($arr);
            }else{
                $this->e_msg('失败了');
            }
        }
    }

    public function sub(){
            
        $zhun = Db::name('zhun') ->where('id',input('id'))->field('type,content')->find();
        $content = unserialize($zhun['content']);
        $crr = [
            'id' => 0,
            'sta_name' => '自提',
            'send_num' => 0,
        ];
        // if($zhun['type']==0){
        foreach ($content as $k => $v) {

            $mer = Db::name('sto_mer')
                ->where("id",$v['mer_id'])
                ->field('id,mer_logo,mer_name')
                ->find();
            $content[$k]['mer'] = $mer;

            $list = Db::name("sto_mer_sta")
                ->alias('ms')
                ->where('ms.mer_id',$mer['id'])
                // ->join('sto_mer m','ms.mer_id=m.id')
                ->join('sto_stage s','ms.sta_id=s.id')
                ->field('ms.*,s.sta_account,s.sta_name')
                ->select();

            array_unshift($list, $crr);
            $content[$k]['yi'] = $list;

            // $yi = explode(',',$yi['mer_stage']); 
            // $content[$k]['yi'] = Db::name('sto_stage')->where('id','in',$yi)->field('id,sta_name,sta_address')->select();
        
        }
        $this->msg($content);
    }

    public function add(){
        if (is_post()) {
            $d = input('post.shoplis');            
            $addr_id = input('post.addr_id');
            $addr = Db::name('user_addr')->where('id',$addr_id)->find();
            if(!$addr){
                $this->e_msg('请传地址');
            }
            $str = '';
            Db::startTrans();
            try {
                foreach ($d as $k => $v) {
                    $ord_number = GetRandStr(10);

                    if($v['yi_id']){
                        $stage = Db::name('sto_mer_sta')->where('id',$v['yi_id'])->find();
                        $send_num = $stage['send_num'];
                    }else{
                       $send_num = 0;
                    }
                    $arr = [
                       'mer_id' => $v['mer_id'],
                       'mer_logo' => $v['mer']['mer_logo'],
                       'mer_name' => $v['mer']['mer_name'],
                       'yi_id'  => $v['yi_id'], 
                       'send_num'  => $send_num, 
                       'us_id'  => $this->user['id'],
                       'ord_add_time' => date('Y-m-d H:i:s'),
                       'ord_number' => $ord_number,
                       'addr_id'  => $addr_id,
                       'addr_name'  => $addr['addr_name'],
                       'addr_tel'  => $addr['addr_tel'],
                       'addr_stree'  => $addr['addr_stree'],
                       'addr_detail'  => $addr['addr_detail'],
                    ];
                    $ord_num = 0;
                    $ord_money = 0;
                    if($send_num){
                        $ord_money += $send_num;
                    }
                    foreach ($v['arr'] as $key => $value) {
                        $prod = Db::name('sto_prod')
                            ->where('id',$value['prod_id'])
                            ->field('id,prod_pic,prod_name,prod_price,prod_price_yuan,prod_zone')
                            ->find();
                        if($this->user['us_is_vip']){
                            $price = $prod['prod_price'];
                        }else{
                            $price = $prod['prod_price_yuan'];
                        }
                        $ord_num += $value['cart_num'];
                        $ord_money += $value['cart_num']*$price;
                        $brr = [
                            'ord_number' => $ord_number,
                            'prod_id' => $value['prod_id'], 
                            'prod_name' => $prod['prod_name'], 
                            'prod_pic' => $prod['prod_pic'], 
                            'prod_zone' => $prod['prod_zone'], 
                            'det_num' => $value['cart_num'], 
                            'det_price' => $price, 
                        ];
                        Db::name('ord_det')->insert($brr);
                        if(array_key_exists('id', $value)){
                            Db::name('sto_cart')->where('id',$value['id'])->delete();
                        }
                        Db::name('sto_prod')->where('id',$value['prod_id'])->setInc('prod_sales',$value['cart_num']);
                    }
                    $arr['ord_num'] = $ord_num;
                    $arr['ord_money'] = $ord_money;
                    $id = Db::name('ord')->insertGetId($arr);
                    $str = $str?$str.','.$id:$id;
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
            }
            if($str){
                $crr = [
                    'code' => 1,
                    'msg' => '提交成功,等待支付',
                    'id' => $str,
                ];
            }else{
                $crr = [
                    'code' => 0,
                    'msg' => '提交失败',
                ];
            }
            $this->msg($crr);
        }
    }
    

    public function index(){
        if(is_post()){
            $this->map[] = ['us_id','=',$this->user['id']];
            $this->map[] = ['ord_is_delete','=',0];
            if(input('post.status')!=''){
                $this->map[] = ['ord_status','=',input('post.status')];
            }
            // dump($this->map);
            $list = model("Ord")->chaxun($this->map,$this->order,$this->size);
            $this->msg($list);
        }else{
            $this->e_msg('get');
        }
    }

    //详情
    public function detail(){
        if(is_post()){
            $info = model('Ord')->with('det')->where('id',input('id'))->find();
            $info['yi'] = Db::name('sto_stage')->where('id',$info['yi_id'])->find();
            $this->msg($info);
        }else{
            $this->e_msg('get');
        }
    }

    //取消订单
    public function cancle(){
        if(is_post()){
            $d = input('id');
            $info = model('Ord')->with('det')->where('id',$d)->find();
            if($info['ord_status']!=0){
                $this->e_msg('该订单状态不是待支付状态');
            }
            $arr = [
                'id' => $d,
                'ord_status' => 3,
                'ord_cancle_time' => date('Y-m-d H:i:s'),
            ];
            $rel = Db::name('ord')->update($arr);
            if($rel){
                foreach ($info['det'] as $k => $v) {
                    $prod = Db::name('sto_prod')->where('id',$v['prod_id'])->find();
                    if($prod){
                        Db::name('sto_prod')->where('id',$v['prod_id'])->setDec('prod_sales',$v['det_num']);
                    }
                }
                $this->s_msg('取消订单成功');
            }else{
                $this->e_msg('取消失败');
            }
        }else{
            $this->e_msg('get');
        }
    }

    //确认收货
    public function receive(){
        if(is_post()){
            $d = input('id');
            $info = model('ord')->where('id',$d)->field('id,us_id,ord_money,ord_status')->find();
            if($info['ord_status']!=1){
                $this->e_msg('该用户状态不是待收货状态');
            }
            $arr = [
                'ord_status' => 2,
                'ord_finish_time' => date('Y-m-d H:i:s'),
            ];
            $rel = Db::name('ord')->where('id',$d)->update($arr);
            if($rel){
                // 抽取%1 给直推
                if($this->user['us_level']!=0 && $this->user['us_pid']){    
                    model("User")->direct_pro($this->user['us_pid'],$info['ord_money']);
                }
                
                $this->s_msg('确认收货成功');
            }else{
                $this->e_msg('收货失败');
            }
        }else{
            $this->e_msg('get');
        }
    }

    /*
        订单删除 
        前台就看不到了
     */
    public function del(){
        if(is_post()){
            $d = input('id');
            $info = Db::name('ord')->where('id',$d)->find();
            if($info['ord_status']!=2){
                $this->e_msg('该订单状态不是已完成状态');
            }
            $arr = [
                'ord_is_delete' => 1,
            ];
            $rel = Db::name('ord')->where('id',$d)->update($arr);
            if($rel){
                $this->s_msg('删除成功');
            }else{
                $this->e_msg('删除失败');
            }
        }else{
            $this->e_msg('get');
        }
    }
}
