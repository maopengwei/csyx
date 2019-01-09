<?php

namespace app\index\controller;
use think\Db;
use think\Request;
use app\common\controller\Api;

/**
 * 商城
 */
class Mer extends Api
{

    public function index() {
        $store = model("StoMer")->where('id',input('mer_id'))->find();
        $cate = model("StoMerProd")
            ->alias('s')
            ->where('s.mer_id', $store['id'])->limit(3)
            ->join('sto_cate c','s.cate_id=c.id')
            ->where('c.cate_status',1)
            ->field('cate_name,c.id')
            ->group('s.cate_id')
            ->select()->toArray();
        
        $arr = [
            'cate_name' => '热销榜',
            'id' => 0,
        ];
        $brr = array_unshift($cate,$arr);
        $data = [
            0 => $store,
            1 => $cate,
        ];
        $this->msg($data);
    }

    public function prod(){
        $mer_id = input('mer_id');
        $cate_id = input('cate_id');
        if($cate_id){
             $list = model('StoMerProd')
                // ->where('cate_id',$cate_id)
                // ->where('mer_id',$mer_id)
                // ->select();

                ->alias('s')
                ->join('sto_prod p','s.prod_id=p.id')
                ->where('s.cate_id', '=', $cate_id)
                ->where('s.mer_id','=',$mer_id)
                ->where('p.prod_status','=',1)
                ->field('s.id,p.prod_name,p.id,p.prod_price,p.prod_price_yuan,p.prod_pic,s.mp_num')
                ->select();
                
        }else{
            $list = model('StoMerProd')
                ->alias('s')
                ->join('sto_prod p','s.prod_id=p.id')
                ->where('s.mer_id', $mer_id)
                ->where('p.prod_status',1)
                ->where('p.prod_is_hot',1)
                ->field('p.prod_name,p.id,p.prod_price,p.prod_price_yuan,p.prod_pic,s.mp_num')
                ->select();
        }

        /*if(input('cate_id')){
             $list = model('StoProd')
                ->where('cate_id', input('cate_id'))
                ->where('prod_status',1)
                ->order('prod_sort desc,id desc')
                ->select();
        }else{
             $list = model('StoProd')
             ->where('prod_is_hot',1)
             ->where('mer_id',input('mer_id'))
             ->where('prod_status',1)
             ->order('prod_sort desc,id desc')
             ->select();
        }*/
        $this->msg($list);
    }

    //
    public function det(){
        if(is_post()){
            $id = input('post.id');
            $content = Db::name('sto_prod')->where('id',$id)->find();
            $content['logo'] = explode(',',$content['prod_logo']);
            $this->msg($content);
        }else{
            $this->e_msg('get');
        }
    }


    public function list(){
        $mer = Db::name('sto_mer')->field('id,mer_name')->select();
        $this->msg($mer);
    }

}
