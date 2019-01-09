<?php
namespace app\common\model;

use think\Model;
// use think\model\concern\SoftDelete;

/**
 * 门店产品
 */
class StoMerProd extends Model {
	// use SoftDelete;
	// protected $deleteTime = 'delete_time';


	//关联用户表
	public function user() {
		return $this->hasOne('User', 'id', 'us_id');
	}

	//详情
	public function detail($where, $field = "*") {
		return $this->with('user')->where($where)->field($field)->find();
	}
	//查询
	public function chaxun($map, $order, $size, $field = "*") {
		$list = $this->with('user')->where($map)->order($order)->field($field)->paginate($size, false, [
			'query' => request()->param()]);
		return $list;
	}

	/**
	 * 添加
	 * @param  [array] $data [description]
	 * @return [bool]       [description]
	 */
	public function tianjia($mer_id,$cate_id,$prod_id,$mp_num) {
		
		$arr = [
			'mer_id' 	=> $mer_id,
			'cate_id' 	=> $cate_id,
			'prod_id'	=> $prod_id,
			'mp_num'    => $mp_num,
			'mer_add_time'		 => date('Y-m-d H:i:s'),
		];

		$rel = $this->insertGetid($arr);

		/*
		
		$number = $this->order('id desc')->value('mer_account');
		if ($number) {
			$bb = substr($number, -5);
			$cc = substr($number, 0, 3);
			$dd = $bb + 1;
			$new_number = $cc . $dd;
		} else {
			$new_number = 'mer10001';
		}
		$arr['mer_account'] = $new_number;*/

		return x_code('添加成功',1);
		/*
		
		return ['code' => 1,'msg' => '添加成功'];*/
	}

}
