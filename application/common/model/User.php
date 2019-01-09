<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;
use think\Db;
use mine\PassN;
use app\common\logic\Up;

/**
 *
 */
class User extends Model {
	use SoftDelete;
	protected $deleteTime = 'delete_time';

	public function parent() {
		return $this->hasOne('User', 'id', 'us_pid');
	}
	// 状态
	public function getStatusTextAttr($value, $data) {
		$array = [
			0 => '被禁用',
			1 => '正常',
		]; 
		return $array[$data['us_status']];
	}
	public function getLevelTextAttr($value,$data){
		return cache('level')[$data['us_level']]['cal_name'];
	}
	//父账号
	public function getPtelAttr($value, $data) {
		if ($data['us_pid']) {
			return $this->where('id', $data['us_pid'])->value('us_account');
		} else {
			return '空';
		}
	}
	//详情
	public function detail($where, $field = "*") {
		
		return $this->with('parent')->where($where)->field($field)->find();
	}
	//查询
	public function chaxun($map, $order, $size, $field = "*") {
		return $this->where($map)->order($order)->field($field)->paginate($size, false, [
			'query' => request()->param()]);
	}
	/**
	 * 添加
	 * @param  [array] $data [description]
	 * @return [bool]       [description]
	 */
	public function tianjia($da) {

		

		// $tel_count = $this->where('us_tel', $da['us_tel'])->count();
		// if ($tel_count) {
		// 	return [
		// 		'code' => 0,
		// 		'msg' => '该手机号已存在',
		// 	];
		// }
		
		$acc_count = $this->where('us_account', $da['us_account'])->count();
		if ($acc_count) {
			return [
				'code' => 0,
				'msg' => '该账号已存在',
			];
		}
		if($da['p_vip_account']){
			$pinf = model("User")->where('us_vip_account', $da['p_vip_account'])->find();
			if (count($pinf)) {
				$da['us_pid'] = $pinf['id'];
				$da['us_path'] = $pinf['us_path'] . $pinf['id'].',';
				$da['us_path_long'] = $pinf['us_path_long'] + 1;
			} else {
				return [
					'code' => 0,
					'msg' => '推荐人不存在',
				];
			}
		}
		
		$da['us_add_time'] = date('Y-m-d H:i:s');
		$da['us_head_pic'] = '/static/mobile/img/tu9.jpg';
		$da['us_pwd'] = PassN::mine_encrypt($da['us_pwd']);
		$da['us_safe_pwd'] = PassN::mine_encrypt($da['us_safe_pwd']);
		$rel = $this->insertGetId($da);
		if($rel){
			return [
				'code' => 1,
				'msg' => '注册成功',
				'id' => $rel,
				'us_account' => $da['us_account'],
			];
		}else{
			return [
				'code' => 0,
				'msg' => '注册失败',
			];
		}
		
	}

	/**
	 * 修改
	 * @param  [array] $data  [数据]
	 * @param  [array] $where [条件]
	 * @return [bool]
	 */
	public function editInfo($da) {
		
		if (key_exists('us_pwd',$da) &&$da['us_pwd']!="") {
			$da['us_pwd'] = PassN::mine_encrypt($da['us_pwd']);
		} elseif(key_exists('us_pwd',$da)) {
			unset($da['us_pwd']);
		}
		
		if (key_exists('us_safe_pwd',$da) && $da['us_safe_pwd']!="") {
			$da['us_safe_pwd'] = PassN::mine_encrypt($da['us_safe_pwd']);
		} elseif(key_exists('us_safe_pwd',$da)) {
			unset($da['us_safe_pwd']);
		}
		
		// halt($da);
		model('User')->update($da);
		return [
			'code' => 1,
			'msg' => '修改成功',
		];
	}
	/**
	 * 修改
	 * @param  [array] $data  [数据]
	 * @param  [array] $where [条件]
	 * @return [bool]
	 */
	public function homeEdit($da) {
		
		if (isset($da['us_pwd'])) {
			$da['us_pwd'] = mine_encrypt($da['us_pwd']);
		} elseif(key_exists('us_pwd',$da)) {
			unset($da['us_pwd']);
		}
		
		if (isset($da['us_safe_pwd'])) {
			$da['us_safe_pwd'] = mine_encrypt($da['us_safe_pwd']);
		} elseif(key_exists('us_safe_pwd',$da)) {
			unset($da['us_safe_pwd']);
		}
		
		// halt($da);
		model('User')->update($da);
		return [
			'code' => 1,
			'msg' => '修改成功',
		];
	}
	//送币
	public function songbi($da){
		if($da['song_type']==1){
			return self::usWalChange($da['id'],$da['song_num'],1);
			
		}elseif($da['song_type']==2){
			if($da['song_num']>0){
				$type = 1;
			}else{
				$type = 2;
			}
			return self::usMscChange($da['id'],abs($da['song_num']),$type);
		}
	}
	//奖励变动
	static public function usWalChange($us_id,$num,$type,$name=''){
		$note = array(
			1 => '后台修改',
			2 => '用户提现',
			3 => '提现驳回',
			4 => '获得'.$name.'一代奖励',
			5 => '获得'.$name.'二代奖励',
			6 => '获得'.$name.'三代奖励',
			7 => '获得'.$name.'消费奖励',
			8 => '分红奖励',
		);
		$rel = self::where('id', $us_id)->setInc('us_wal',$num);
		if($rel){
			model('ProWal')->tianjia($us_id,$num,$type,$note[$type]);
			return [
				'code' => 1,
				'msg' => '成功',
			];
		}else{
			return [
				'code'=>0,
				'msg' => '失败',
			];
		}
	}

	//消费奖励
	public function direct_pro($id,$money,$name){
		$uu = Db::name("user")->where("id",$id)->find();
		if(!$uu){
			return;
		}
		self::usWalChange($id,$money,7,$name);
	}

	//等级奖励 


	public function to_vip($id){
		$info = self::where('id',$id)->field('id,us_is_vip,us_vip_end,us_pid')->find();
		// halt($info);
		if(!$info){
			return;
		}
		$rel = 0;
		if($info['us_is_vip']==0){
			$arr = [
				'us_is_vip' => 1,
				'us_vip_time' => date('Y-m-d H:i:s'),
				'us_vip_end' => date('Y-m-d',time()+86400*365),
				'us_vip_account' => 'CHAO'.date('YmdHis').rand(1000,9999),
				'us_level' => 1, 
			];
			$rel = Db::name('user')->where('id',$id)->update($arr);
		}else{
			$time = strtotime($info['us_vip_end']);
			$new = $time+86400*365;
			$nn = date('Y-m-d',$new);
			Db::name('user')->where('id',$id)->setfield('us_vip_end',$nn);
		}

		if($rel){
			//上级发奖励
			//上级升级
			self::dir_pro($id);
			if($info['us_pid']){
				$pp = self::where('id',$info['us_pid'])->field('id,us_level')->find();
				if($pp['us_level']==1){
					Up::upZhu($pp['id'],$pp['us_level']);
				}
			}
		}
	}

	//代数奖励
	public function dir_pro($id){
      	$us = Db::name('user')->where('id',$id)->field('id,us_account,us_path')->find();
      	// $bbb = trim(',',$us['us_path']);
      	// dump($bbb);
      	$path = explode(',',$us['us_path']);
      	$arr = array_reverse($path);
   
      	if(key_exists(1,$arr)){
      		self::usWalChange($arr[1],cache('setting')['cal_xiao_yi'],4,$us['us_account']);
      	}
      	if(key_exists(2,$arr)){
      		self::usWalChange($arr[2],cache('setting')['cal_xiao_er'],5,$us['us_account']);
      	}
      	if(key_exists(3,$arr)){
      		self::usWalChange($arr[3],cache('setting')['cal_xiao_san'],6,$us['us_account']);
      	}
    }
	
}
