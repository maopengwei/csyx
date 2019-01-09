<?php

namespace app\index\controller;

use think\Route;
use app\common\controller\Api;
use think\Db;

class Index extends Api
{
    //轮播图
    public function shuff()
    {  
        $list = model('Shuff')->where('shuff_status',1)->order('shuff_sort desc,id desc')->field('shuff_pic')->select();
        $this->msg($list);
    }
    //分类
    public function gift(){
        $list =model('StoGift')->where('cate_pid', 0)->order('cate_sort desc,id desc')->field('id,cate_pic,cate_name')->select();
        $this->msg($list);
    }
    
    //首页指定产品
    public function prod(){

        $list = model('StoProd')
            ->where('prod_status',1)
            ->where('prod_is_index',1)
            ->where('prod_zone',1)
            ->limit(2)
            ->field('id,prod_name,prod_pic,mer_id')
            ->select();
        $this->msg($list); 
    }

    public function tui(){
        if (input('post.size') != "") {
            $size = input('post.size');
        } else {
            $size = 10;
        }
        $this->map[] = ['mer_is_tui','=',1];
        $this->map[] = ['mer_status','=',1];
        $field = 'id,mer_name,mer_logo';
        $list = model('StoMer')->chaxun($this->map,$this->order,$this->size,$field);
        foreach ($list as $k => $v) {
            $list[$k]['prod'] = model('StoMerProd')
                ->alias('s')
                ->where('s.mer_id',$v['id'])->limit(3)
                ->join('sto_prod p','s.prod_id=p.id')
                // ->join('sto_mer m','s.mer_id=m.id')
                ->where('p.prod_status',1)
                ->field('p.prod_name,p.prod_pic,prod_price,prod_price_yuan')
                ->select();

                // ->order('prod_sort desc,id desc')
                // ->field('id,prod_pic,prod_name,prod_price,prod_price_yuan')
                // ->select();
            $list[$k]['sale'] = 100;
        }
        $this->msg($list);
     }

    //门店列表
    public function index() {
        $where = "AND (mer_status = 1) ";
        // $where = '';
        if (input('post.page') != "") {
            $page = input('post.page');
        } else {
            $page = 1;
        }
        if (input('post.size') != "") {
            $size = input('post.size');
        } else {
            $size = 10;
        }
        if (input('post.scope') != "") {
            $scope = input('post.scope');
        } else {
            $scope = 1000;
        }
        if (input('post.lng') != "") {
            //精度
            $lng = input('post.lng');
        } else {
            $lng = 0;
        }
        if (input('post.lat') != "") {
            //纬度
            $lat = input('post.lat');
        } else {
            $lat = 0;
        }

        if (input('post.mer_keyword')) {
            $keywords = input('post.mer_keyword');
            $where .= "AND (mer_name LIKE '%" . $keywords . "%')";
        }

        if (input('post.cate_id')) {
           $kkkk = input('post.cate_id');
            $where .= "AND (mer_gift LIKE '%" . $kkkk . "%')";
        }

        $dd = 0;
        if (input('post.prod_keyword')) {
            $ww = input('post.prod_keyword');
            // $rra = model('StoProd')->where('prod_name','like','%'.$ww.'%')->group('mer_id')->field('mer_id,id')->select()->toArray();
            $rra = model('StoProd')->where('prod_name','like','%'.$ww.'%')->field('id')->select()->toArray();
            
            if($rra){
                
                $rrb = array_column($rra, 'id');
                // $rrc = implode(',',$rrb);
                $rrc = model('StoMerProd')->where('prod_id','in',$rrb)->group('mer_id')->field('mer_id')->select()->toArray();
                if($rrc){
                    $rrd = array_column($rrc,'mer_id');
                    $rre = implode(',',$rrd);
                    $where .= "AND (id IN (".$rre."))";
                    $dd = 1;
                }else{
                    $where .= 'AND (id = 0)';
                }

            }else{
                $where .= 'AND (id = 0)';
            }
            
        }
       
        // dump($where);
        $juli = $this->getAroundCoordinate($lng, $lat, $scope);
        $p = ($page - 1) * 10;
        $left = $lng - $juli[0];
        $right = $lng + $juli[0];
        $buttom = $lat - $juli[1];
        $top = $lat + $juli[1];
        

        $list = Db::query("SELECT *,SQRT(POWER($lat - mer_latitude, 2) + POWER($lng  - mer_longitude, 2)) AS d FROM new_sto_mer WHERE (mer_longitude BETWEEN $left AND $right) AND (mer_latitude BETWEEN $buttom AND $top)" . $where . "ORDER BY d ASC LIMIT $p,$size");
        // $list = Db::query("SELECT *,SQRT(POWER($lat - mer_latitude, 2) + POWER($lng  - mer_longitude, 2)) AS d FROM new_sto_mer " . $where . "ORDER BY d ASC LIMIT $p,$size");
        foreach ($list as $k => $v) {
            $list[$k]['dis'] = getDistance($lng, $lat, $v['mer_longitude'], $v['mer_latitude']);
            $list[$k]['sale'] = 100;
            if($dd==0){
                $list[$k]['prod'] = model('StoMerProd')
                    ->alias('s')
                    ->where('s.mer_id',$v['id'])->limit(3)
                    ->join('sto_prod p','s.prod_id=p.id')
                    ->where('p.prod_status',1)
                    ->field('p.prod_name,p.prod_pic,prod_price,prod_price_yuan')
                    ->select();
            }else{
                $list[$k]['prod'] = model('StoMerProd')
                    ->alias('s')
                    ->where('s.mer_id',$v['id'])->limit(3)
                    ->join('sto_prod p','s.prod_id=p.id')
                    ->where('p.prod_status',1)
                    ->field('p.prod_name,p.prod_pic,p.prod_price,p.prod_price_yuan')
                    ->select();

                    // ->where('mer_id',$v['id'])->where('prod_name','like','%'.$ww.'%')->limit(3)->order('prod_sort desc,id desc')
                    // ->field('id,prod_pic,prod_name,prod_price,prod_price_yuan')
                    // ->select();
            }
        }
        if ($list == []) {
            $this->e_msg('数据为空');
        }
        $this->msg($list);
    }

    public function mer() {
        $where = '';
        // if (input('post.page') != "") {
        //     $page = input('post.page');
        // } else {
        //     $page = 1;
        // }
        if (input('post.size') != "") {
            $this->map[] = ['size','=',input('size')];
        } else {
            $this->map[] = ['size','=',input('size')];
        }

        $list = model("StoMer")->chaxun($this->map,$this->order,$this->size);
        $this->msg($list);
    }

    public function getAroundCoordinate($lng, $lat, $distance) {
        $dlng = 2 * asin(sin($distance / (2 * 6371)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);

        $dlat = $distance / 6371;
        $dlat = rad2deg($dlat);
        return array($dlng, $dlat);
    }

    // public function getAroundCoordinate($lng, $lat, $distance) {
    //     $dlng = 2 * asin(sin($distance / (2 * 6371)) / cos(deg2rad($lat)));
    //     $dlng = rad2deg($dlng);

    //     $dlat = $distance / 6371;
    //     $dlat = rad2deg($dlat);
    //     return array($dlng, $dlat);
    // }
    
}