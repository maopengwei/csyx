<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 会员卡
 */
class PayRecord extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';
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
	public static function tianjia($pay_type, $us_id, $pay_num, $pay_lei, $note,$ll = 0) {

		$array = array(
			'pay_type' => $pay_type, //微信 支付宝
			'us_id' => $us_id,   //用户
			'pay_num' => $pay_num,  //金额
			'pay_lei' => $pay_lei,   //类型
			'pay_note' => $note,     //关联
			'pay_add_time' => date('Y-m-d H:i:s'),
		);

		$rel = self::insertGetId($array);
		if($rel){
			if($pay_lei==1){
				$arr = explode(',',$note);
				foreach ($arr as $k => $v) {
					if($v){
						$brr = [
							'ord_pay_time' => date('Y-m-d H:i:s'),
							'ord_status'   => 1,
						];
						Ord::where('id',$v)->update($brr);
					}
					
				}
			}elseif($pay_lei==2){
				$id = $note;
				$brr = [
					'bao_pay_time' => date('Y-m-d H:i:s'),
					'bao_status'   => 1,
				];
				OrdBao::where('id',$id)->update($brr);
			}
		}
		return $rel;
	}
	/**
	 * 修改
	 * @param  [array] $data  [数据]
	 * @param  [array] $where [条件]
	 * @return [bool]
	 */
	public function xiugai($data, $where) {
		return $this->save($data, $where);
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
	//真实姓名
	public function getTypeTextAttr($value, $data) {
		$arr = [
			1 => '微信',
			2 => '支付宝',
			3 => '银行卡',
			4 => '会员卡',
			5 => '线下',
		];

		return $arr[$data['pay_type']];
	}
}
