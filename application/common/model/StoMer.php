<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 *产品
 */
class StoMer extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';


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
	public function tianjia($mer_name,$mer_pic,$us_id) {
		
		$arr = [
			'mer_name' 	=> $mer_name,
			'mer_pic' 	=> $mer_pic,
			'us_id'		 => $us_id,
			'mer_add_time'		 => date('Y-m-d H:i:s'),
		];

		$number = $this->order('id desc')->value('mer_account');
		if ($number) {
			$bb = substr($number, -5);
			$cc = substr($number, 0, 3);
			$dd = $bb + 1;
			$new_number = $cc . $dd;
		} else {
			$new_number = 'mer10001';
		}
		$arr['mer_account'] = $new_number;

		$rel = $this->insertGetid($arr);
		
		return ['code' => 1,'msg' => '添加成功'];
	}



	//状态
	public function getStatusTextAttr($value, $data) {
		$array = [
			0 => '未上线',
			1 => '上线中',
		];
		return $array[$data['mer_status']];
	}

	//是否自己商户
	public function getZiTextAttr($value, $data) {
		$array = [
			0 => '本家',
			1 => '它家',
		];
		return $array[$data['mer_is_zi']];
	}

	//用户账号
	public function getUsTextAttr($value, $data) {
		if ($data['us_id'] == "") {
			return '';
		}
		$name = model('User')->where('id', $data['us_id'])->value('us_account');
		return $name;
	}
	//真实姓名
	public function getUsNameAttr($value, $data) {
		if ($data['us_id'] == "") {
			return '';
		}
		$name = model('User')->where('id', $data['us_id'])->value('us_real_name');
		return $name;
	}

}
