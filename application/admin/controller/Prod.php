<?php
namespace app\admin\controller;
use think\Db;
/**
 * 商品
 */
class Prod extends Common {

	public function __construct() {
		parent::__construct();
	}

	public function getCate(){
		$mer_id = input('post.mer_id');
		if($mer_id){
			$list = Db::name('sto_cate')->where("mer_id",$mer_id)->where("cate_status",1)->order("cate_sort desc,id desc")->field('id,cate_name')->select();
			return [
				'code'=> 1,
				'data' => $list,
			];
		}else{
			$this->error('请求失败');
		}
	}




	/*------------------商品*/
	public function index() {
		if (is_post()) {
			$rst = model('StoProd')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
			if ($rst) {
				$this->success('修改成功');
			} else {
				$this->error("修改失败");
			}
		}

		$this->map[] = ['prod_zone','=',0];

		if (input('get.keywords')) {
			if(input('get.keywords')=='自营'){
				$this->map[] = ['mer_id', '=', 0];
			}else{
				$id = model("StoMer")->where('mer_name', trim(input('get.keywords')))->value('id');
				if($id){
					$this->map[] = ['mer_id', '=', $id];
				}else{
					$this->map[] = ['id','=',0];
				}
			}
		}

		if (is_numeric(input('get.status'))) {
			$this->map[] = ['prod_status', '=', input('get.status')];
		}

		if (is_numeric(input('get.zone'))) {
			$this->map[] = ['prod_zone', '=', input('get.zone')];
		}
		
		if (input('get.prod_name') != "") {
			$this->map[] = ['prod_name', 'like', "%" . trim(input('get.prod_name')) . "%"];
		}
		
		$list = model('StoProd')->chaxun($this->map, $this->order, $this->size);

		$this->assign(array(
			'list' => $list,
		));
		return $this->fetch();
	}

	public function add() {
		
		if (is_post()) {
			$data = input('post.');

			$file = request()->file('file');
			if($file){
				$base = uploads($file);
				if($base['code']){
					$data['prod_pic'] = $base['path'];
				}else{
					return $base;
				}
			}

			//验证器
			$validate = validate('Prod');
			$res = $validate->scene('add')->check($data);
			if (!$res) {
				$this->error($validate->getError());
			}


			if(!$data['cate_id']){
				return x_code('请选择产品分类');
				// ['code'=>0,'msg'=>'请选择产品分类']
			}

			$rel = model('StoProd')->tianjia($data);
			return $rel;
		}
		$cate = model('StoCate')->where('cate_pid', 0)->where('cate_status',1)->order('cate_sort desc')->select();
        // foreach ($cate as $k => $v) {
        //     $cate[$k]['son'] = model('StoCate')->where('cate_pid', $v['id'])->select();
        // }
		$this->assign(array(
			'mer' => model("StoMer")->select(),
			'cate' => $cate,
		));

		return $this->fetch();
	}

	public function edit() {
		if (is_post()) {
			$data = input('post.');
			if(!key_exists('prod_logo',$data)){
				$this->error('至少上传一张图片');
			}else{
				$data['prod_logo'] = implode(',',$data['prod_logo']);
			}

			$validate = validate('Prod');
			$res = $validate->scene('edit')->check($data);
			if (!$res) {
				$this->error($validate->getError());
			}
			$data['prod_is_gai'] = 0;
			$rel = model('StoProd')->update($data);
			if ($rel) {
				$this->success('保存成功');
			} else {
				$this->error('您并没有做出修改');
			}
		}

		$info = model("StoProd")->get(input('id'));
		$info['prod_logo'] = explode(',', $info['prod_logo']);
		// $list = model("Cate")->where('st_id', $info['st_id'])->select();
		// $name = model("Store")->where('id', $info['st_id'])->value('st_name');
		$this->assign(array(
			'info' => $info,
			// 'st_pic' => $st_pic,
			// 'name' => $name,
		));
		return $this->fetch();
	}

	public function del(){
		if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('id不存在');
        }
        $info = model('StoProd')->get($id);
        if ($info) {
            $rel = Db::name('sto_prod')->where('id',$id)->delete();
            if ($rel) {
            	Db::name('sto_mer_prod')->where('prod_id',$info['id'])->delete();
                $this->success('删除成功');
            } else {
                $this->error('请联系网站管理员');
            }
        } else {
            $this->error('数据不存在');
        }
	}

	/*----------------------商品属性*/
	public function attr(){
		if(is_post()){
			$data = input('post.');
			if(!$data['attr_id']){
				$this->error('请选择属性名');
			}
			$attr = model("StoAttr")->detail(['id'=>$data['attr_id']]);
			$arr = [
				'attr_pid' => $attr['attr_pid'],
				'attr_id' => $data['attr_id'],
				'prod_id' => $data['prod_id'],
			];
			$rel = model('StoProdAttr')->tianjia($arr);
			return $rel;
		}
		$id = input('get.id');
		$prod = model('StoProd')->get($id);
		$attr = model('StoAttr')->where('cate_id',$prod['cate_id'])->where('attr_pid',0)->select();
		foreach ($attr as $k => $v) {
			$attr[$k]['son'] = model('StoAttr')->where('attr_pid',$v['id'])->select();
		}
		$this->order = 'attr_pid';
		$this->map[] = ['prod_id','=',$id];
		$list = model('StoProdAttr')->chaxun($this->map, $this->order, $this->size);
		$this->assign(array(
			'attr' => $attr,
			'list' => $list,
			'prod_id' => $id,
		));

		return $this->fetch();
	}

	public function attr_del(){
		if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('id不存在');
        }
        $info = model('StoProdAttr')->get($id);
        if ($info) {
            $rel = db('sto_prod_attr')->where('id',$id)->delete();
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
