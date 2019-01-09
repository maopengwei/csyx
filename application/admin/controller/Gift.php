<?php
namespace app\admin\controller;

/**
 * 店铺分类
 */
class Gift extends Common
{

    public function __construct()
    {
        parent::__construct();
    }



    // 分类列表
    public function index()
    {

        if (is_post()) {

            $rst = model('StoGift')->where('id',input('id'))->update([input('key')=>input('value')]);
            if($rst){
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }
       
        $list = model('StoGift')->where('cate_pid', 0)->order('cate_sort desc,id desc')->select();
        foreach ($list as $k => $v) {
            $list[$k]['son'] = model('StoGift')->where('cate_pid', $v['id'])->select();
        }
        $count = count($list);
        $this->assign(array(

            'list'=> $list,
            'count'=> $count,

        ));
        return $this->fetch();
    }
    

    //添加分类
    public function add()
    {
        if (is_post()) {
            $data = input('post.');
            if (!input('cate_name') || model('StoGift')->where('cate_name', input('cate_name'))->count() > 0) {
                $this->error('分类名为空或已有此分类');
            }
            $rst = model('StoGift')->tianjia($data);
           return $rst;
        } else {
            $cate = model('StoGift')->where('cate_pid', 0)->select();
            $this->assign('cate', $cate);
            return $this->fetch();
        }
    }
    //编辑分类
    public function edit()
    {
        if (is_post()) {
            
            $data = input('post.');
            // if (!$data['cate_name'] || model('InGift')->where('cate_name', input('cate_name'))->count() > 1) {
            //     $this->error('分类名为空或已有此分类');
            // }
            // if(!is_numeric($data['cate_sort'])){
            //      $this->error('排序字段不能为空');
            // }
            model('StoGift')->update($data);
            $this->success('修改成功');

        } else {
            $cate = model('StoGift')->where('cate_pid', 0)->select();
            $info = model("StoGift")->detail(['id'=>input('id')]);
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
        $info = model('StoGift')->detail(['id'=>$id]);
       
        if ($info) {
            if (model('StoGift')->where('cate_pid', $info['id'])->find()) {
                $this->error('该分类下面有子分类所以不能删除');
            }
            $rel = db('StoGift')->where('id', $id)->delete();
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