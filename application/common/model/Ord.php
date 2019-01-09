<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 订单
 */
class Ord extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';

	// 关联用户
	public function user() {
		return $this->hasOne('User', 'id', 'us_id')->field('us_account');
	}
	// 
	public function det()
    {
        return $this->hasMany('OrdDet','ord_number','ord_number')
        			->field('prod_id,ord_number,det_price,det_num,prod_pic,prod_zone,prod_name');
    }

	//详情
	public function detail($where, $field = "*") {
		return $this->with('det')->where($where)->field($field)->find();
	}
	//查询
	public function chaxun($map, $order, $size, $field = "*") {
		$list = $this->with('det')->where($map)->order($order)->field($field)->paginate($size, false, [
			'query' => request()->param()]);
		return $list;
	}
	
	/**
	 * 添加
	 * @param  [array] $data [description]
	 * @return [bool]       [description]
	 */
	// public function tianjia($d) {
	// 	$d['cart_add_time'] = date('Y-m-d H:i:s');
	// 	$rel = $this->insertGetid($d);
	// 	return ['code' => 1,'msg' => '添加成功'];
	// }
	public function getStatusTextAttr($value,$data){
		$arr = [
			0=> '未支付',
			1=> '未完成',
			2=> '已完成',
			3=> '已取消',
		];
		return $arr[$data['ord_status']];
	}
}
