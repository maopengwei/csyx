<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 订单
 */
class OrdBao extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';

	// 关联用户
	public function user() {
		return $this->hasOne('User', 'id', 'us_id')->field('id,us_account');
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
	
	public function getStatusTextAttr($value,$data){
		$arr = [
			0=> '未支付',
			1=> '未完成',
			2=> '已完成',
			3=> '已取消',
		];
		return $arr[$data['bao_status']];
	}
}
