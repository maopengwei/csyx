<?php
namespace app\admin\controller;
/**
 * 轮播图
 */
class Shuff extends Common
{
	
	public function shuff(){

		if (is_numeric(input('get.status'))) {
			$this->map[] = ['shuff_status', '=', input('get.status')];
		}
		if (input('get.keywords')) {
			$this->map[] = ['shuff_name', 'like', "%".input('get.keywords')."%"];
		}
		
		$list = model('Shuff')->chaxun($this->map,$this->order,$this->size);
		$this->assign(array(
			'list'=>$list,
		));
		return $this->fetch();
	}
	public function add(){
		if (is_post()) {
			$data = input('post.');
			// halt($data);
			$file = request()->file('file');

			if($file){
				$base = uploads($file);
				if($base['code']){
					$data['shuff_pic'] = $base['path'];
				}else{
					return $base;
				}
			}
			//验证器
			$validate = validate('Other');
			$res = $validate->scene('addshuff')->check($data);
			if (!$res) {
				$this->error($validate->getError());
			}

			$rel = model('Shuff')->tianjia($data);
			return $rel;
		}
		return $this->fetch();
	}
	public function edit(){
		
		if(is_post()){
			$data = input('post.');
			$file = request()->file('file');
			if($file){
				$base = uploads($file);
				if($base['code']){
					$data['shuff_pic'] = $base['path'];
				}else{
					return $base;
				}
			}
			$rel = model('Shuff')->update($data);
			return ['code'=>1,'msg'=>'修改成功'];
		}else{
			$this->assign('info',model("Shuff")->where('id',input('id'))->find());
			return $this->fetch();
		}
		
	}

	public function del(){
		if (input('post.id')) {
			$id = input('post.id');
		} else {
			$this->error('id不存在');
		}
		$info = db('shuff')->where('id',$id)->find();
		if ($info) {
			$rel = model('Shuff')->destroy($id);
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