<?php
namespace app\admin\controller;

/**
 * 店铺分类
 */
class Stage extends Common
{

    public function __construct()
    {
        parent::__construct();
    }

    // 列表
    public function index()
    {

        if(is_post()){
            $rst = model('StoStage')->where('id',input('id'))->update([input('key')=>input('value')]);
            if($rst){
                $this->success('修改成功');
            }else{
                $this->error('修改失败');
            }
        }
        $list = model('StoStage')->chaxun($this->map,$this->order,$this->size);
        $this->assign(array(
            'list'=> $list,
        ));
        return $this->fetch();

    }
    

    //添加
    public function add()
    {
        if (is_post()) {
            $data = input('post.');

            if (!input('sta_name') || model('StoStage')->where('sta_name', input('sta_name'))->count() > 0) {
                $this->error('驿站名为空或已有此驿站名');
            }
            $rst = model('StoStage')->tianjia($data);
           return $rst;
        } else {
            return $this->fetch();
        }
    }
    //编辑
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
            model('StoStage')->update($data);
            $this->success('修改成功');

        } else {
            $info = model("StoStage")->detail(['id'=>input('id')]);
            $this->assign(array(
                'info' => $info,
            ));
            return $this->fetch();
        }
    }
    //删除
    public function del()
    {
        if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('非法操作');
        }
        $info = model('StoStage')->detail(['id'=>$id]);
       
        if ($info) {
          
            $rel = db('StoStage')->where('id', $id)->delete();
            if ($rel) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('非法操作');
        }
    }

    //驿站列表
    public function posi() {
        $rel = model("StoStage")->select();
        return $rel;
    }

    //驿站定位
    public function position() {
        if (is_post()) {
            $data = input("post.");
            // $validate = validate('Verify');
            // $rst = $validate->scene('editTude')->check($data);
            // if (!$rst) {
            //  $this->error($validate->getError());
            // }
            $rel = model('StoStage')->where('id',input('post.id'))->update($data);
            if ($rel) {
                $this->success('修改成功');
            } else {
                $this->error('您未进行修改');
            }
        }
        $info = model('StoStage')->get(input('get.id'));
        $this->assign(array(
            'info' => $info,
        ));
        return $this->fetch();
    }


}