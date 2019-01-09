<?php
namespace app\admin\controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * @todo 会员管理 查看，状态变更，密码重置
 */
class User extends Common {

	//用户列表
	public function index() {
		if (is_post()) {

			if(input('act')=='get'){
				return model('User')->detail(['id'=>input('id')]);
			}
			$rst = model('User')->where('id',input('id'))->update([input('key')=>input('value')]);
			if($rst){
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}
		if (input('get.keywords')) {
			$this->map[] = ['us_account|us_tel|us_real_name', '=', input('get.keywords')];
		}
		if (is_numeric(input('get.status'))) {
			$this->map[] = ['us_status', '=', input('get.status')];
		}
		if (is_numeric(input('get.level'))) {
			$this->map[] = ['us_level', '=', input('get.level')];
		}
		if (input('get.start')) {
			$this->map[] = ['us_active_time', '>=', input('get.start')];
		}
		if (input('get.end')) {
			$this->map[] = ['us_active_time', '<=', input('get.end')];
		}
		if (input('get.a') == 1) {
			$list = model("User")->where($this->map)->select();
			// $url = action('Excel/user', ['list' => $list]);
			$bb = env('ROOT_PATH') . "public\user.xlsx";
			if (file_exists($bb)) {
				$aa = unlink($bb);
			}
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setCellValue('A1', '账户名')
				->setCellValue('B1', '收货人')
				->setCellValue('C1', '收货电话')
				->setCellValue('D1', '收货地址')
				->setCellValue('E1', '激活时间');
			$i = 2;
			foreach ($list as $k => $v) {
				$sheet->setCellValue('A' . $i, $v['us_account'])
					->setCellValue('B' . $i, $v['us_addr_person'])
					->setCellValue('C' . $i, $v['us_addr_tel'])
					->setCellValue('D' . $i, $v['us_addr_addr'])
					->setCellValue('E' . $i, $v['us_active_time']);
				$i++;
			}
			$writer = new Xlsx($spreadsheet);
			$writer->save('user.xlsx');
			return "http://" . $_SERVER['HTTP_HOST'] . "/user.xlsx";
		}

		$list = model('User')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array(
			'yuming' => $_SERVER['HTTP_HOST'],
			'list' => $list,
			'level' => cache('level'),
		));
		return $this->fetch();
	}
	//添加
	public function add() {
		if (is_post()) {
			$data = input('post.');
			$rel = model('User')->tianjia($data);
			return $rel;
		}else{
			return $this->fetch();
		}
	}
	//升级等级
	public function level(){
		$info = model('User')->get(input('id'));
		if(is_post()){
			$data = input('post.');

			if($info['us_level']==0){
				if($data['us_level']==1){
					$type = 1;
				}elseif($data['us_level']==2){
					$type = 3;
				}
			}else{
				$type = 15;
			}
			$money = buy_type($type);
			$note = buy_note($type);
				
			$rel = model('PayRecord')->tianjia(5,$info['id'],$money,$type,$note);
			if($rel){
				$this->success('操作成功');
			}else{
				$this->error('操作失败');
			}
			
		}
		$this->assign('info', $info);
		return $this->fetch();
	}
	//升级经销商
	public function up(){
		$info = model('User')->get(input('id'));
		if(is_post()){
			$data = input('post.');
			$aa = get_type($info['us_jibie'],$data['us_jibie']-1);
			if($aa['code']){
				$money = buy_type($aa['type']);
				$note = buy_note($aa['type']);
				$rel = model('PayRecord')->tianjia(5,$info['id'],$money,$aa['type'],$note,$data['type']);
				if($rel){
					$this->success('操作成功');
				}else{
					$this->error('操作失败');
				}
			}else{
				return $aa;
			}
			
		}
		$this->assign('info', $info);
		return $this->fetch();
	}
	//修改
	public function edit() {
		if (is_post()) {
			$da = input('post.');
			
			$rel = model('User')->editInfo($da);
			return $rel;
		}else{
			$this->assign('info', model('User')->get(input('id')));
			$this->assign('level', cache('level'));
			return $this->fetch();
		}
	}
	/*---------送币*/
	public function song(){
		if(is_post()){
			$da = input('post.');
			$rel = model("User")->songbi($da);
			return $rel;
		}else{
			$this->assign('info', model('User')->get(input('id')));
			return $this->fetch();
		}
		
	}
	//团队
	public function team() {
		if (is_post()) {
			$info = model('User')->where('us_account|us_tel|us_real_name', input('post.us_account'))->field('id,us_path,us_pid,us_account,us_level,us_tel')->find();
			if (!$info) {
				return [
					'code' => 0,
					'msg' => "查无此人",
				];
			}
			$qu_ye = qu_yeji($info['id']);
			
			$zz = "左:" . $qu_ye['l_ye'].  "," . $qu_ye['l_num'];
			$yy = "右:" . $qu_ye['r_ye'].  "," . $qu_ye['r_num'];
			$calcu = cache('level')[$info['us_level']];
			/*
				$zz = "左:" . $qu_ye['l_ye'].  "," . $qu_ye['l_num'];
				$yy = "右:" . $qu_ye['r_ye'].  "," . $qu_ye['r_num'];
					$znote[$key]['zuo'] = "左:" . $qu_ye['l_ye'].  "," . $qu_ye['l_num'];
					$znote[$key]['you'] = "右:" . $qu_ye['r_ye'].  "," . $qu_ye['r_num'];
				$calcu = cache('level')[$arr['us_level']];
			 */
			$base = array(
				'id' => $info['id'],
				'pId' => $info['us_pid'],
				'name' => $info['us_account'] . "," . $info['us_tel'].','.$zz.','.$yy.','.$calcu['cal_name'],
			);
			$znote[] = $base;
			$where[] = array('us_path', 'like', $info['us_path'] . "," . $info['id'] . "%");
			$list = Model('User')->where($where)->field('id,us_pid,us_account,us_level,us_tel')->select();
			foreach ($list as $k => $v) {
				$quye = qu_yeji($v['id']);
				$z = "左:" . $quye['l_ye'].  "," . $quye['l_num'];
				$y = "右:" . $quye['r_ye'].  "," . $quye['r_num'];
				$caca = cache('level')[$v['us_level']];

				$base = array(
					'id' => $v['id'],
					'pId' => $v['us_pid'],
					'name' => $v['us_account'] . "," . $v['us_tel'].','.$z.','.$y.','.$caca['cal_name'],
				);
				$znote[] = $base;
			}
			return [
				'code' => 1,
				'data' => $znote,
			];
		}
		if(input('get.id')){
			$this->assign('us_account',input('get.id'));
		}
		return $this->fetch();
	}

	//地址列表
	public function addr() {
		if (is_post()) {
			$rst = model('User_addr')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			if ($rst) {
				$this->success('修改成功');
			} else {
				$this->error('修改失败');
			}
			return $rst;
		}
		if (input('get.id')) {
			$this->map[] = ['us_id', '=', input('get.id')];
		} else {
			$this->error("非法操作");
		}
		$list = model('User_addr')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array(
			'list' => $list,
			'name' => model('User')->where('id', input('get.id'))->value('us_account'),
		));
		return $this->fetch();
	}

	
	//地址修改
	public function addr_edit() {
		if (is_post()) {
			$data = input("post.");
			$validate = validate('Verify');
			$rst = $validate->scene('editAddr')->check($data);
			if (!$rst) {
				$this->error($validate->getError());
			}
			unset($data['id']);
			$rel = model('Store')->xiugai($data, ['id' => input('post.id')]);
			if ($rel) {
				$this->success('修改成功');
			} else {
				$this->error('您未进行修改');
			}
		}
		$info = model("User_addr")->get(input('get.id'));
		$this->assign(array(
			'info' => $info,
		));
		return $this->fetch();
	}
	public function position() {
		return $list = model("User_addr")->where('us_id', input('post.us_id'))->select();
	}
	public function is_jing() {
		$id = input('get.id');
		$info = model("User")->get($id);
		if ($info['us_is_jing'] != 1) {
			return [
				'code' => 0,
				'msg' => '该用户不是待进入节点图状态',
			];
		}
		if ($info['us_jibie'] == 0) {
			return [
				'code' => 0,
				'msg' => '该用户不是经销商',
			];
		}
		return [
			'code' => 1,
		];
	}
	public function tupu() {
		if (is_post()) {

			if (input('post.us_account')) {
				$info = model('User')->where('us_account|us_tel|us_real_name', input('post.us_account'))->find();
				if (!$info) {
					return [
						'code' => 0,
						'msg' => '该用户不存在',
					];
				}
			}

			$this->map[] = ['us_path', 'like', $info['us_path'].$info['id'] . ",%"];
			$this->map[] = ['us_path_long', '<=', $info['us_path_long'] + 2];
			$list = model('User')->where($this->map)->select()->toArray();
			array_push($list, $info);
			$rrrrrarr = [
				0 => '[已禁用]',
				1 => '[正常]',
			];
			$znote = [];
			foreach ($list as $k => $v) {
				
				$arr = $v;
				$status = $rrrrrarr[$arr['us_status']];
				$calcu = cache('level')[$arr['us_level']];
				$znote[$k]['name'] = $arr['us_account'];
				$znote[$k]['tel'] = $arr['us_tel'];
				$znote[$k]['zuo'] = $arr['us_real_name'];
				$znote[$k]['you'] = $status;
				$znote[$k]['level'] = $calcu['cal_name'];
				$znote[$k]['key'] = $arr['id'];
				$znote[$k]['parent'] = $arr['us_pid'];
				if ($arr['us_head_pic']) {
					$znote[$k]['source'] = $arr['us_head_pic'];
				}


			}
			
			return [
				'code' => 1,
				'data' => $znote,
				'ptel' =>$info['p_tel'],
			];
		} else {
			$this->assign(array(
				'us_account' => input('id')?:model('User')->find()['us_account'],
			));
			return $this->fetch();
		}
	}
	

	//所有删除
	public function del() {

		if (input('post.id')) {
			$id = input('post.id');
		} else {
			$this->error('id不存在');
		}

		$info = model('User')->get($id);
		if ($info) {
			// if ($info['us_status'] != 0) {
			// 	$this->error('已经激活的会员不能删除');
			// }
			$list = model('User')->where('us_aid',$id)->select();
			if(count($list)){
				$this->error('该会员节点下已有用户，不能删除');
			}
			$rel = model('User')->destroy($id);
			if ($rel) {
				$this->success('删除成功');
			} else {

				$this->error('请联系网站管理员');
			}
		} else {
			$this->error('数据不存在');
		}
	}

	public function active(){
		if(is_post()){

			$da = input('post.');
			$info = model('User')->where('id',$da['id'])->find();
			if($info['us_status']==0){
				$all_money = 3300;
                $wal_type = 3;  //扣除茶币类型
                $peng_type = 5; //对碰奖励类型
			}else{
				$this->error('该用户已经被激活');
			}

			 $brr = [
                'id' => $da['id'],
                'us_rel' => $info['us_rel']+$all_money,
                'us_active_time'=>date('Y-m-d H:i:s'),
                'us_status'=>1,
            ];

			$rel = model('User')->editInfo($brr);

			if($rel['code']){
				
				model('User')->direct_pro($da['id']);	
				
				// 层碰奖励 对碰奖励
				model('User')->yeji($info['id'],$all_money,$peng_type);

				return [
					'code'=>1,
					'msg'=>'激活成功',
				];

			}else{

				return [
					'code'=>0,
					'msg'=>'激活失败',
				];
				
			}
		}
	}
	
	protected function scerweima($url = '', $logo = '') {
		require_once __DIR__ . '\qrcode.php';
		$value = $url; //二维码内容
		$errorCorrectionLevel = 'H'; //容错级别
		$matrixPointSize = 7; //生成图片大小
		//生成二维码图片
		$path = '/uploads/erweima/' . date('YmdHis') . rand(1000, 9999) . '.png';
		$filename = $_SERVER['DOCUMENT_ROOT'] . $path;
		\QRcode::png($value, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		// $logo = $_SERVER['DOCUMENT_ROOT'] . '/static/admin/img/tou.jpg'; //准备好的logo图片
		$QR = $filename; //已经生成的原始二维码图
		if (file_exists($logo)) {
			$QR = imagecreatefromstring(file_get_contents($QR)); //目标图象连接资源。
			$logo = imagecreatefromstring(file_get_contents($logo)); //源图象连接资源。
			$QR_width = imagesx($QR); //二维码图片宽度
			$QR_height = imagesy($QR); //二维码图片高度
			$logo_width = imagesx($logo); //logo图片宽度
			$logo_height = imagesx($logo); //logo图片高度
			$logo_qr_width = $QR_width / 4; //组合之后logo的宽度(占二维码的1/5)
			$scale = $logo_width / $logo_qr_width; //logo的宽度缩放比(本身宽度/组合后的宽度)
			$logo_qr_height = $logo_height / $scale; //组合之后logo的高度
			$from_width = ($QR_width - $logo_qr_width) / 2; //组合之后logo左上角所在坐标点
			//重新组合图片并调整大小
			/*
	         *  imagecopyresampled() 将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中
*/
			imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
		}
		// header('Content-Type: image/png');
		//输出图片
		$path1 = '/uploads/erweima/' . date('YmdHis') . rand(1000, 9999) . '.png';
		imagepng($QR, $_SERVER['DOCUMENT_ROOT'] . $path1);
		imagedestroy($QR);
		imagedestroy($logo);
		return $path1;
	}
}
