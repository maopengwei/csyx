<?php
namespace app\mer\controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\Db;
/**
 * @todo
 */
class Order extends Common {

	// ------------------------------------------------------------------------
	// 订单列表
	public function index() {
		if (is_post()) {

			$rst = model('Order')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			return $rst;
		}
		$this->map[] = ['mer_id','=',session('mer_id')];
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_tel', input('get.keywords'))->value('id');
			if ($us_id) {
				$array = model('StoOrder')->where('us_id',$us_id)->field('order_number')->select()->toArray();
				$arr = array_column($array,'order_number');
				$this->map[] = ['us_id', 'in', $arr];
			}
		}
		if (input('get.prod_name')) {
			$ord_number = Db::name('ord_det')->where('prod_name','like','%'.input('get.prod_name').'%')->field('ord_number')->select();
			if($ord_number){
				$arr = array_column($ord_number,'ord_number');
				$this->map[] = ['ord_number', 'in', $arr];
			}else{
				$this->map[] = ['ord_number','=',0];
			}
		}

		// $this->map[] = ['prod_zone','=',0];
		if (input('get.status') != "") {
			$this->map[] = ['ord_status', '=', input('get.status')];
		}

		if (input('get.mer_name') != "") {
			$this->map[] = ['mer_id', 'like', '%'.input('get.mer_name').'%'];
		}

		if (input('get.order_number') != "") {
			$this->map[] = ['ord_number', '=', input('get.order_number')];
		}
		if (input('get.start')) {
			$this->map[] = ['ord_add_time', '>=', input('get.start')];
		}
		if (input('get.end')) {
			$this->map[] = ['ord_add_time', '<=', input('get.end')];
		}
		
		if (input('get.a') == 1) {
			$list = model("StoOrderDetail")->with('order')->where($this->map)->select();
			// $url = action('Excel/user', ['list' => $list]);
			$bb = env('ROOT_PATH') . "public\order.xlsx";
			if (file_exists($bb)) {
				$aa = unlink($bb);
			}
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			
			$sheet->setCellValue('A1', '订单编号')
				->setCellValue('B1', '客户姓名')
				->setCellValue('C1', '店铺')
				->setCellValue('D1', '产品')
				->setCellValue('E1', '产品类型')
				->setCellValue('F1', '总价')
				->setCellValue('G1', '单价')
				->setCellValue('H1', '数量')
				->setCellValue('I1', '状态')
				->setCellValue('J1', '添加时间');
			$i = 2;
			foreach ($list as $k => $v) {
				$sheet->setCellValue('A' . $i, $v['order_number'])
					->setCellValue('B' . $i, $v->order->user['us_account'])
					->setCellValue('C' . $i, $v['mer_text'])
					->setCellValue('D' . $i, $v['prod_name'])
					->setCellValue('E' . $i, $v['zone_text'])
					->setCellValue('F' . $i, $v['order_money'])
					->setCellValue('G' . $i, $v['prod_price'])
					->setCellValue('H' . $i, $v['prod_num'])
					->setCellValue('I' . $i, $v['status_text'])
					->setCellValue('J' . $i, $v['detail_add_time']);
				$i++;
			}
			
			$writer = new Xlsx($spreadsheet);
			$writer->save('order.xlsx');
			return "http://" . $_SERVER['HTTP_HOST'] . "/order.xlsx";
		}
		$list = model('Ord')->chaxun($this->map, $this->order, $this->size);

		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();
	}

	public function detail() {
		
		$id = input('id');
		$info = model('Ord')->with('det')->where('id',$id)->find();
		if (is_post()) {
			$da  = input('post.');
			if($info['detail_status']<1 || $info['detail_status']>3){
				return ['code'=>0,'msg'=>'该订单状态不支持发货'];
			}
			$da['detail_status'] = 2;
			$da['detail_delive_time'] = date('Y-m-d H:i:s');
			$res = model("StoOrderDetail")->update($da);
			// if($res && $info['mer_id']){
			// 	$prod = model("StoProd")->get($info['prod_id']);
			// 	$mer = model('StoMer')->get($info['mer_id']);
			// 	$num = $prod['prod_price']*$info['prod_num']*cache('setting')['huo_calcu']/100;
			// 	model("ProWal")->tianjia($mer['us_id'],$num,15); 
			// }
			return ['code'=>1,'msg'=>'成功'];
		}
		// $id = input('get.id');
		// $info = model('StoOrderDetail')->detail(['id'=>$id]);
		$this->assign(array(
			'info' => $info,
		));
		return $this->fetch();
	}

	public function finish(){
		if(is_post()){
			$id = input('post.id');
			$info = model('StoOrderDetail')->detail(['id'=>$id]);
			$time = unixtime('day',-10);
			$ten = date('Y-m-d H:i:s',$time);
			
			if($info['detail_status']!=2 || $info['detail_delive_time']>$ten ){
				return ['code'=>0,'msg'=>'该订单不是待收货状态或发货时间小于10天'];
			}
			$data = array(
	            'detail_finish_time' => date('Y-m-d H:i:s'),
	            'detail_status' => 3,
	        );
	        $rel = model('StoOrderDetail')->where('id',$id)->update($data);
	        if ($rel) {
	            $this->success('确定收货成功');
	        } else {
	            $this->error('确定收货失败');
	        }
	    }
		
	}	

	public function del(){
		if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('id不存在');
        }
        $info = model('StoOrderDetail')->get($id);
        if ($info) {
            $rel = model('StoOrderDetail')->destroy($id);
            if ($rel) {
                $this->success('删除成功');
            } else {
                $this->error('请联系网站管理员');
            }
        } else {
            $this->error('数据不存在');
        }
	}
}
