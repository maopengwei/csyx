<?php
namespace app\index\controller;
use think\Db;

class Baod extends Base 
{
    
    //产品列表
    public function index(){
    	$prod = Db::name('sto_prod')
    		->where('prod_zone',1)
    		->where('prod_status',1)
    		->field('id,prod_pic,prod_name,prod_price')
    		->select();
    	$this->msg($prod);
    }

    //产品详情
    public function xq(){
    	if(is_post()){
            $id = input('post.id');
            $content = Db::name('sto_prod')->where('id',$id)->find();
            $content['logo'] = explode(',',$content['prod_logo']);
            $this->msg($content);
        }else{
            $this->e_msg('get');
        }
    }

   	public function add(){

   		/*
   			地址id
   			产品id
   		 */
      $d = input('post.');
   	  $prod = model('StoProd')->where('id',$d['prod_id'])->find();
      $addr = Db::name('user_addr')->where('id',$d['addr_id'])->find();
      $arr = [
        'prod_id' => $prod['id'], 
        'prod_pic' => $prod['prod_pic'], 
        'prod_name' => $prod['prod_name'], 
        'prod_price' => $prod['prod_price'],
        'us_id' => $this->user['id'],
        'bao_status' => 0, 
        'bao_number' => 1, 
        'bao_add_time' => date('Y-m-d H:i:s'), 
        'addr_id' => $addr['id'], 
        'addr_name' => $addr['addr_name'], 
        'addr_stree' => $addr['addr_stree'], 
        'addr_detail' => $addr['addr_detail'], 
        'addr_tel' => $addr['addr_tel'], 
      ];

      $rel = Db::name('ord_bao')->insertGetId($arr);
      if($rel){
        $brr = [
          'code' => 1,
          'msg' => '报单成功，请支付',
          'id' => $rel,
        ];
      }else{
        $brr =[
          'code' => 0,
          'msg' => '报单失败',
        ];
      }
      $this->msg($brr);
   	}

   	public function order(){
   		if(is_post()){
            $this->map[] = ['us_id','=',$this->user['id']];
            if(input('post.status')!=''){
                $this->map[] = ['bao_status','=',input('post.status')];
            }
            $list = model("OrdBao")->where($this->map)->select();
            $this->msg($list);
        }else{
            $this->e_msg('get');
        }
   	}

   	public function ordXq(){
      if(is_post()){
          $info = model('OrdBao')->where('id',input('id'))->find();
          if($info['mer_id']){
              $info['mer_name'] = Db::name('sto_mer')->where('id',$info['mer_id'])->value('mer_name');
          }else{
              $info['mer_name'] = '空';
          }
          $this->msg($info);
      }else{
          $this->e_msg('get');
      }
    }
    //取消订单
    public function cancle(){
        if(is_post()){
            $d = input('id');
            $info = model('OrdBao')->with('det')->where('id',$d)->find();
            if($info['bao_status']!=0){
                $this->e_msg('该订单状态不是待支付状态');
            }
            $arr = [
                'bao_status' => 3,
                'bao_cancle_time' => date('Y-m-d H:i:s'),
            ];
            $rel = Db::name('OrdBao')->where('id',$d)->update($arr);
            if($rel){
                $prod = Db::name('sto_prod')->where('id',$info['prod_id'])->find();
                if($prod){
                    Db::name('sto_prod')
                      ->where('id',$prod['id'])
                      ->setDec('prod_sales',$info['bao_number']);
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
            $info = model('OrdBao')->where('id',$d)->find();
            if($info['bao_status']!=1){
                $this->e_msg('该用户状态不是待收货状态');
            }

            if(input('mer_id') == ""){
                $this->e_msg('请传入提货门店');
            }

            $arr = [
                'bao_status' => 2,
                'bao_finish_time' => date('Y-m-d H:i:s'),
                'mer_id' => input('post.mer_id'),
            ];
            $rel = Db::name('OrdBao')->where('id',$d)->update($arr);
            if($rel){
                /*
                 会员是否是贵宾会员 
                    不是
                        变成新贵宾会员   
                            判断上级是否升级
                        上级奖励3代
                    是
                        增加自己的年限

                */
                model('User')->to_vip($this->user['id']);
                $this->s_msg('确认收货成功');
            }else{
                $this->e_msg('收货失败');
            }
        }else{
            $this->e_msg('get');
        }
    }

}
