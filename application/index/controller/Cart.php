<?php

namespace app\index\controller;

use think\Request;
use think\Db;
/**
 * 商城
 */
class Cart extends Base
{

    // public function initialize(){
    //     // parent::initialize();
    //     $this->user = model('User')->where("us_tel",13000000000)->find();
    // }

    public function add(){


        $arr = input('crr');
        $mer_id = input('mer_id');
        // halt(input('crr'));
        /*
           $arr = [
                0 => [
                    'prod_id' => 1,
                    'num' => 2,
                ],
                1=> [
                    'prod_id' => 2,
                    'num' => 3,
                ];
           ];

         */

        foreach ($arr as $k => $v) {
            $info = Db::name('sto_cart')->where('us_id',$this->user['id'])->where('prod_id',$v['prod_id'])->find();
            $mer = Db::name('sto_cart')->where('us_id',$this->user['id'])->where('prod_id',$v['prod_id'])->find();
            if($info){
                Db::name('sto_cart')->where("id",$info['id'])->setInc('cart_num',$v['cart_num']);
            }else{
                $brr = [];
                $brr = [
                    'us_id' => $this->user['id'],
                    'prod_id' => $v['prod_id'],
                    'cart_num' => $v['cart_num'],
                    'mer_id' => $mer_id,
                    'cart_add_time' => date('Y-m-d H:i:s'),
                ];
                Db::name('sto_cart')->insert($brr);
            }
        }

        $this->s_msg('加入成功');

    }

    //购物车列表
    public function index(){
            $this->map[] = ['us_id','=',$this->user['id']];
            $list = model('StoCart')->with('mer')->where($this->map)->field('mer_id')->group('mer_id')->select();
            foreach ($list  as $k => $v) {
                $list[$k]['arr'] = model('StoCart')
                    ->alias('c')
                    ->where('c.us_id',$this->user['id'])
                    ->where('c.mer_id',$v['mer_id'])
                    ->join('sto_prod p','c.prod_id=p.id')
                    ->where('p.prod_status',1)
                    ->field('c.*,p.prod_name,p.prod_pic,p.prod_price,p.prod_price_yuan')
                    ->select();
            }
            $this->msg($list);
    }

    public function sub(){
        if(is_post()){

            $this->map[] = ['in','in',$arrid];
            $list = model('StoCart')->with('prod')->where($this->map)->order('id desc')->select();
            $this->msg($list);
        }else{
            $this->s_msg('get');
        }
    }

    /*
        量量
        购物车 id
        数量   num    
    */
    public function num(){
        if(is_post()){
            $d = input('post.');
            $info = Db::name('sto_cart')->where('id',input('id'))->where('us_id',$this->user['id'])->find();
            if($info){
                Db::name('sto_cart')->where('id',input('id'))->setfield('cart_num',input('cart_num'));
                $this->s_msg('修改成功');
            }else{
                $this->e_msg('信息不存在');
            }
        }else{
            $this->s_msg('get');
        }
    }
    public function del(){
        if(is_post()){
            $d = input('post.');
            $info = Db::name('sto_cart')->where('id',input('id'))->where('us_id',$this->user['id'])->select();
            if($info){
                Db::name('sto_cart')->where('id',input('id'))->delete();
                $this->s_msg('删除成功');
            }else{
                $this->e_msg('信息不存在');
            }
          
        }else{
            $this->s_msg('get');
        }
    }

}
