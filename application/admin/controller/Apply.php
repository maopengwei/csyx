<?php
namespace app\admin\controller;

use think\Container;
use think\Db;
/**
 * 商家
 */
class Apply extends Common {



	/*-----------------申请*/

	public function index(){

		if(is_post()){
			$data = input('post.');
			$info = model('StoApply')->detail(['id'=>$data['id']]);
			model("StoApply")->update($data);
			if($data['apply_status']==1){
				model("User")->where('id',$info['us_id'])->setfield('us_is_mer',1);
				// $data  = [
				// 	'us_id'=>$info['us_id'],
				// 	'apply_name'=>$info['apply_name'],
				// ];
				model('StoMer')->tianjia($info['apply_name'],'',$info['us_id']);
				$this->success('审核通过');
			}else{
				$this->success('已被驳回');
			}
		}
		if (input('get.keywords')) {
			$us_id = model("User")->where('us_account|us_tel|us_real_name', input('get.keywords'))->value('id');
			if (!$us_id) {
				$us_id = 0;
			}
			$this->map[] = ['us_id', '=', $us_id];
		}
		if (is_numeric(input('get.status'))) {
			$this->map[] = ['apply_status', '=', input('get.status')];
		}
		$list = model('StoApply')->chaxun($this->map, $this->order, $this->size);

		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();


	}
	public function edit() {
		
		$info = model('StoApply')->detail(['id' => input('get.id')]);
		
		$this->assign(array(
			
			'info' => $info,
		
		));
		return $this->fetch();
	
	}
	public function del(){
		if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('id不存在');
        }
        $info = model('StoApply')->get($id);
        if ($info) {
            $rel = model('StoApply')->destroy($id);
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
