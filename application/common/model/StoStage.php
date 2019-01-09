<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\Db;
/**
 * 驿站
 */
class StoStage extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	// public function cate(){
	// 	return $this->hasOne('StoCate', 'id', 'cate_id');
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
	/**
	 * 添加
	 * @param  [array] $data [description]
	 * @return [bool]       [description]
	 */
	public function tianjia($data) {
		$data['sta_add_time'] = date('Y-m-d H:i:s');
		
		// 驿站
		$work_number = $this->order('id desc')->value('sta_account');
		if ($work_number) {
			$bb = substr($work_number, -5);
			$cc = substr($work_number, 0, 2);
			$dd = $bb + 1;
			$sta_account = $cc . $dd;
		} else {
			$sta_account = 'yz10001';
		}

		$data['sta_account'] = $sta_account;
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

	public function getStatusTextAttr($value, $data) {
		$arr = [
			0 => '禁止',
			1 => '正常',
		];
		return $arr[$data['sta_status']];
	}
	public function getCateTextAttr($value, $data) {
		return model('StoCate')->where('id', $data['cate_id'])->value('cate_name');
	}
	public function getPicTextAttr($value, $data) {
		$arr = explode(',',$data['prod_pic']);
		return $arr;
	}
}
