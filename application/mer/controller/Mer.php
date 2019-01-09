<?php
namespace app\mer\controller;

use think\Container;
use think\Db;
/**
 * 商家
 */
class Mer extends Common {

	

	/*--------------------------商家*/
	public function index() {
		if (is_post()) {

			$rst = model('StoMer')->where('id',input('id'))->update([input('post.key') => input('post.value')]);
			if($rst){
				$this->success("修改成功");
			}else{
				$this->error('修改失败');
			}
		}
		$this->map[] = ['id','=',session("mer_id")];
		$list = model('StoMer')->chaxun($this->map, $this->order, $this->size);
		
		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();

	}


	public function add() {
		if (is_post()) {
			$data = input('post.');

			$validate = validate('Mer');
			$res = $validate->scene('add')->check($data);
			if (!$res) {
				$this->error($validate->getError());
			}


			$uu = Model("User")->where('us_account',$data['us_account'])->find();
			if(!$uu){
				return ['code'=>0,'msg'=>'该用户不存在'];
			}
			$vv = Model("User")->where('us_account',$data['us_account'])->where('us_tel',$data['us_tel'])->find();
			if(!$uu){
				return ['code'=>0,'msg'=>'该用户手机号不匹配'];
			}
			if($vv['us_is_mer']){
				return ['code'=>0,'msg'=>'该用户已经是商家了'];
			}

			$rel = model('StoMer')->tianjia($data['mer_name'],$data['mer_pic'],$vv['id']);
			if($rel['code']){
				model("User")->where('id',$vv['id'])->setfield('us_is_mer',1);
			}
			return $rel;
		}
		return $this->fetch();
	}

	public function edit() {

		if (is_post()) {
			$data = input('post.');


			$validate = validate('Mer');
			$rst = $validate->scene('edit')->check($data);
			if (!$rst) {
				$this->error($validate->getError());
			}

			$data['mer_gift'] = implode(',',$data['mer_gift']);
			
			$rel = model('StoMer')->where('id',input('post.id'))->update($data);
			return ['code'=>1,'msg'=>'修改成功'];
		}else{

			$info = model('StoMer')->detail(['id'=>input('get.id')]);
			$gift = Db::name('sto_gift')->where('cate_pid',0)->where('cate_status',1)->order('cate_sort desc,id desc')->select();
			$this->assign(array(
				'info' => $info,
				'gift' => $gift,
			));
			return $this->fetch();
		}
	}

	public function del(){
		if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('id不存在');
        }
        $info = model('StoMer')->get($id);
        if ($info) {
        	model("User")->where('id',$info['us_id'])->setfield('us_is_mer',0);
            $rel = model('StoMer')->destroy($id);
            if ($rel) {
                $this->success('删除成功');
            } else {
                $this->error('请联系网站管理员');
            }
        } else {
            $this->error('数据不存在');
        }
	}

	//门店定位
	public function positioning() {
		if (is_post()) {
			$data = input("post.");
			$validate = validate('Verify');
			$rst = $validate->scene('editTude')->check($data);
			if (!$rst) {
				$this->error($validate->getError());
			}
			$rel = model('Store')->xiugai($data, ['id' => input('post.id')]);
			if ($rel) {
				$this->success('修改成功');
			} else {
				$this->error('您未进行修改');
			}
		}
		$info = model('Store')->get(input('get.id'));
		$this->assign(array(
			'info' => $info,
		));
		return $this->fetch();
	}
	
	//门店列表
	public function posi() {
		$rel = model("StoMer")->select();
		return $rel;
	}

	//门店定位
	public function position() {
		if (is_post()) {
			$data = input("post.");
			// $validate = validate('Verify');
			// $rst = $validate->scene('editTude')->check($data);
			// if (!$rst) {
			// 	$this->error($validate->getError());
			// }
			$rel = model('StoMer')->where('id',input('post.id'))->update($data);
			if ($rel) {
				$this->success('修改成功');
			} else {
				$this->error('您未进行修改');
			}
		}
		$info = model('StoMer')->get(input('get.id'));
		$this->assign(array(
			'info' => $info,
		));
		return $this->fetch();
	}

	public function get_cate() {
		$list = model('StoCate')->where('st_id', input('post.id'))->select();

		if ($list!='') {
			return $data = [
				'code' => 1,
				'data' => $list,
			];
		} else {
			return $data = [
				'code' => 0,
			];
		}
	}
	public function getProd() {
		$cate_id = input('post.cate_id');
		$list = model('StoProd')
			->where('cate_id',$cate_id)
			->where('prod_status',1)
			->where('prod_zone',0)
			->select();

		if ($list!='') {
			return $data = [
				'code' => 1,
				'data' => $list,
			];
		} else {
			return $data = [
				'code' => 0,
			];
		}
	}

	public function prodDet(){
		$prod_id = input('post.prod_id');
		$list = model('StoProd')->where('id',$prod_id)->find();

		if ($list!='') {
			return $data = [
				'code' => 1,
				'data' => $list,
			];
		} else {
			return $data = [
				'code' => 0,
			];
		}
	}

	/*--查询用户*/
	public function get_us(){
		$info = model("User")->where('us_account',input('us_account'))->find();

		if($info){
			if($info['us_is_mer']){
				return ['code'=>2,'msg'=>'该用户已经是商家了'];
			}
			return ['code'=>1,'data'=>$info];
		}else{
			return ['code'=>0];
		}
	}

	//商品驿站
	public function stage(){

		if(is_post()){
			$d = input('post.');
			$rel = Db::name("sto_mer_sta")->where('id',$d['id'])->setfield('send_num',$d['val']);
			if($rel){
				return x_code("成功");
			}else{
				return x_code("失败",1);
			}


			/*if(array_key_exists('sta_id',$d)){
				$id = $d['mer_id'];
				$aa = implode(',',$d['sta_id']);
				$rel = db("sto_mer")->where('id',$id)->setfield('mer_stage',$aa);
			}else{
				$rel = db("sto_mer")->where('id',$id)->setfield('mer_stage','');
			}
			if($rel){
				$this->success('保存成功');
			}else{
				$this->error('您并没有做出修改');
			}*/
		}


		$list = Db::name("sto_mer_sta")
			->alias('ms')
			->where('ms.mer_id',input('mer_id'))
			// ->join('sto_mer m','ms.mer_id=m.id')
			->join('sto_stage s','ms.sta_id=s.id')
			->field('ms.*,s.sta_account,s.sta_name')
			->select();

		// 	->select();
		// halt($list);
		// // ->join('sto_prod p','s.prod_id=p.id')

		// // $arr = explode(',',$mer_stage);
		// $this->map[] = ['sta_status','=',1];
		// $list = model('StoStage')->where($this->map)->select();
        $this->assign(array(
            'list'=> $list,
        	// 'mer_stage' => $mer_stage,
        	'mer_id' => input('mer_id'),
        ));
        return $this->fetch();
	}

	public function stage_add(){
		$mer_id = input('mer_id');
		if(is_post()){
			$d = input('post.');
			if($d['mer_id']=="" || $d['sta_id']=='' || $d['send_num'] == ''){
				$this->error('非法操作');
			}
			$info = Db::name('sto_mer_sta')
				->where('mer_id',$d['mer_id'])
				->where('sta_id',$d['sta_id'])
				->find();
			if($info){
				$this->error('该驿站已经有了');
			}

			$rel = Db::name('sto_mer_sta')->insert($d);
			if($rel){
				$this->success('成功');
			}else{
				$this->error('失败');
			}

			halt($d);
		}
		$stage =  model('StoStage')->field('id,sta_account,sta_name')->select();
		$this->assign(array(
			'stage' => $stage,
			'mer_id' => $mer_id,
		));	
		return $this->fetch();
	}
	public function staDet(){
		$sta_id = input('post.sta_id');
		$list = model('StoStage')->where('id',$sta_id)->field('sta_name')->find();

		if ($list!='') {
			return $data = [
				'code' => 1,
				'data' => $list,
			];
		} else {
			return $data = [
				'code' => 0,
			];
		}
	}
	public function sta_del(){

		if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('id不存在');
        }
        $info = model('StoMerSta')->get($id);
        if ($info) {
            $rel = model('StoMerSta')->where('id',$id)->delete();
            if ($rel) {
                $this->success('删除成功');
            } else {
                $this->error('请联系网站管理员');
            }
        } else {
            $this->error('数据不存在');
        }

	}



	//门店产品
	public function prod(){
		$mer_id = input('mer_id');
		if(is_post()){
			$d = input('post.');
			$rel = Db::name('sto_mer_prod')->where('id',$d['id'])->setfield('mp_num',$d['val']);
			if($rel){
				return x_code('修改成功',1);
			}else{
				return x_code('修改失败');
			}
		}
		$list = model('StoMerProd')
					->alias('s')
					->join('sto_prod p','s.prod_id=p.id')
					// ->join('sto_mer m','s.mer_id=m.id')
					->where('s.mer_id',$mer_id)
					->where('p.prod_status',1)
					->field('p.*,s.mp_num,s.mp_add_time')
					->select();

		$count = count($list);
		$this->assign(array(
			'mer_id' => $mer_id,
			'list' => $list,
			'count' => $count,
		));
		return $this->fetch();
	}

	public function prod_add(){
		$mer_id = input('mer_id');
		
		if(is_post()){
			$d = input('post.');
			if($d['mer_id']=='' || $d['prod_id']=='' || $d['cate_id']==''){
				$this->error('非法操作');
			}
			$info = model('StoMerProd')
				->where('mer_id',$d['mer_id'])
				->where('cate_id',$d['cate_id'])
				->where('prod_id',$d['prod_id'])
				->find();
			if($info!=''){
				$this->error('您的门店已经有这个商品了');
			}
			$d['mp_add_time'] = date('Y-m-d H:i:s');
			$rel = Db::name('sto_mer_prod')->insert($d);
			if($rel){
				return x_code('添加成功',1);
			}else{
				return x_code('添加失败');
			}
		}
		$cate =  model('StoCate')->select();
		$this->assign(array(
			'cate' => $cate,
			'mer_id' => $mer_id,
		));	
		return $this->fetch();
	}

	public function prod_del(){

		if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('id不存在');
        }
        $info = Db::name('sto_mer_prod')->where('id',$id)->find();
        if ($info) {
        	// model("StoMerProd")->where('id',$info['us_id'])->setfield('us_is_mer',0);
            $rel = Db::name('sto_mer_prod')->where('id',$id)->delete();
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
