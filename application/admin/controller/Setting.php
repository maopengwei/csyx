<?php
namespace app\admin\controller;

use Cache;
/**
 * @todo 配置信息管理
 */
class Setting extends Common {
	public function _initialize() {
		parent::_initialize();
	}
	// --- ---------------------------------------------------------------------
	//
	public function index() {
		if (is_post()) {
			$data = input('post.');
			model('SysConfig')->xiugai($data);
			$this->success('修改成功');
		}
		return $this->fetch();
	}

	//系统参数
	public function system() {

		if($this->request->isPost()){
			$d = $this->request->post();

			if($d['type']==1){
				$rel = db('sys_level')->where('id',$d['id'])->setfield($d['key'],$d['val']);
			}else{
				$rel = db('jing')->where('id',$d['id'])->setfield($d['key'],$d['val']);
			}
			if($rel){
				Cache::clear();
			}
		}else{
			$this->assign(array(
				'list'=> cache('level'),
			));
			return $this->fetch();
		}
	}
	
	//
	public function edit() {
		if (is_post()) {
			$data = input('post.');
			$rel = model('Calcu')->xiugai($data);
			return $rel;
		}

		$k = input('id') - 1;
		$this->assign(array(
			'k' => $k,
		));
		return $this->fetch();
	}

	
	//项目文档
	public function api() {
		return $this->fetch();
	}
	public function document() {
		$path = env('ROUTE_PATH');
		$swagger = \Swagger\scan($path);
		header('Content-Type: application/json');
		echo $swagger;
	}
}
