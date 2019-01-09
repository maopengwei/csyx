<?php
namespace app\admin\controller;
use think\Db;
/**
 * 产品分类
 */
class Cate extends Common
{

    public function __construct()
    {
        parent::__construct();
    }
    // 分类列表
    public function index()
    {
       if(is_post()){
            $d = input('post.');
            $rel = Db::name('sto_cate')->where('id',$d['id'])->setfield($d['key'],$d['value']);
            if($rel){
               return x_code('成功',$rel); 
            }else{
                return x_code('失败');
            }
       }

        $list = model('StoCate')
            // ->where('mer_id',input('mer_id'))
            ->where('cate_pid', 0)
            ->order('cate_sort desc,id desc')
            ->select();
       
        $count = count($list);
        $this->assign(array(
            'mer_id'    => input('mer_id'),
            'list'      => $list,
            'count'     => $count,
        ));
        return $this->fetch();
    }
    //添加分类
    public function add()
    {
        if (is_post()) {
            $data = input('post.');
            if (!input('cate_name')) {
                $this->error('分类名为空');
            }
            if (model('StoCate')->where('cate_name', input('cate_name'))->where('mer_id',input('mer_id'))->count() > 0) {
                $this->error('已有此分类');
            }
            $rst = model('StoCate')->tianjia($data);
            return $rst;
        } else {
            // $cate = model('StoCate')->where('cate_pid', 0)->select();
            $mer_id = input('mer_id');
            $this->assign('mer_id', $mer_id);
            return $this->fetch();
        }
    }
    //编辑分类
    public function edit()
    {
        if (is_post()) {
            
            $data = input('post.');
            // halt($data);
            if (!input('cate_name')) {
                $this->error('分类名为空');
            }
            if (model('StoCate')->where('cate_name', input('cate_name'))->where('mer_id',input('mer_id'))->count() > 1) {
                $this->error('已有此分类');
            }
            if(!is_numeric($data['cate_sort'])){
                 $this->error('排序字段不能为空');
            }
            model('StoCate')->update($data);
            $this->success('修改成功');

        } else {
            $cate = model('StoCate')->where('cate_pid', 0)->select();
            $info = model("StoCate")->detail(['id'=>input('id')]);
            $this->assign(array(
                'cate' => $cate,
                'info' => $info,
            ));
            return $this->fetch();
        }
    }
    //删除分类
    public function del()
    {
        if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('非法操作');
        }
        $info = model('StoCate')->detail(['id'=>$id]);
       
        if ($info) {
            // if (model('StoCate')->where('cate_pid', $info['id'])->find()) {
            //     $this->error('该分类下面有子分类所以不能删除');
            // }
            //判断产品

            $rel = db('sto_cate')->where('id', $id)->delete();
            if ($rel) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('非法操作');
        }
    }
}