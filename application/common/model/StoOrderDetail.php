<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *产品
 */
class StoOrderDetail extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	
	// 关联order
	public function order() {
		return $this->hasOne('StoOrder', 'order_number', 'order_number');
	}
	// 关联快照
	public function kuai() {
		return $this->hasOne('KuaiProd', 'id', 'kuai_id');
	}
	
	//详情
	public function detail($where, $field = "*") {
		return $this->with('order,kuai')->where($where)->field($field)->find();
	}

	//查询
	public function chaxun($map, $order, $size, $field = "*") {
		$list = $this->with('order,kuai')->where($map)->order($order)->field($field)->paginate($size, false, ['query' => request()->param()]);
		return $list;
	}
	
	/**
	 * 添加
	 * @param  [array] $data [description]
	 * @return [bool]       [description]
	 */
	public function tianjia($data) {
		$data['detail_add_time'] = date('Y-m-d H:i:s');
		$rel = $this->insertGetid($data);
		return ['code' => 1,'msg' => '添加成功'];
	}
	
	/**
	 * 修改
	 * @param  [array] $data  [数据]
	 * @param  [array] $where [条件]
	 * @return [bool]
	 */
	public function xiugai($data, $where) {
		$rel = $this->save($data, $where);
		return [
			'code' => 1,
			'msg' => '修改成功',
			'data' => $rel,
		];
	}

	public function getMerTextAttr($value, $data) {
		if ($data['mer_id'] == 0) {
			return '自营';
		}
		return model('StoMer')->where('id', $data['mer_id'])->value('mer_name');
	}
	public function getZoneTextAttr($value, $data) {
		$arr = [
			0 => '普通商品',
			1 => '报单产品',
		];
		return $arr[$data['prod_zone']];
	}

	public function getTypeTextAttr($value, $data) {
		$arr = [
			0 => '报单',
			1 => '复投报单',
		];
		return $arr[$data['prod_type']];
	}

	public function getStatusTextAttr($value, $data) {
		$arr = [
			0 => '未支付',
			1 => '已付款',
			2 => '已发货',
			3 => '已完成',
			-1 => '已取消',
		];
		return $arr[$data['detail_status']];
	}
	public function getCateTextAttr($value, $data) {
		return model('StoCate')->where('id', $data['cate_id'])->value('cate_name');
	}
	public function getPicTextAttr($value, $data) {
		$arr = explode(',',$data['prod_pic']);
		return $arr;
	}
}
