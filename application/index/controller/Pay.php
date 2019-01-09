<?php
namespace app\index\controller;
use think\Db;
/**
 * 
 */
class Pay extends Base
{
	
	public function index(){

		$id = input('id');
		if(!$id){
			$this->e_msg('请传入id');
		}
		$arr = explode(',',$id);
		$type = input('type');
		if($type==1){
			$money = Db::name('ord')->where('id','in',$arr)->sum('ord_money');
		}elseif($type == 2){
			$money = Db::name('OrdBao')->where('id',$id)->value('prod_price');
		}
		if($money){
			$brr = [
				'code'=>'1',
				'msg' => '成功',
				'money' => $money,
				'us_id' => $this->user['id'],
			];
		}else{
			$brr = [
				'code'=>'0',
				'msg' => '金额为0',
				'money' => 0,
				'us_id' => $this->user['id'],
			];
		}
		$this->msg($brr);
	}
}