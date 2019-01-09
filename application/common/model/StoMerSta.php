<?php
namespace app\common\model;

use think\Model;
// use think\model\concern\SoftDelete;

/**
 * 门店产品
 */
class StoMerSta extends Model {
	// use SoftDelete;
	// protected $deleteTime = 'delete_time';


	//关联用户表
	// public function user() {
	// 	return $this->hasOne('User', 'id', 'us_id');
	// }

	//详情
	public function detail($where, $field = "*") {
		return $this->where($where)->field($field)->find();
	}
	//查询
	public function chaxun($map, $order, $size, $field = "*") {
		$list = $this->where($map)->order($order)->field($field)->paginate($size, false, [
			'query' => request()->param()]);
		return $list;
	}

}
