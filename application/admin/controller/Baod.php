<?php
namespace app\admin\controller;
use think\Db;
/**
 * 报单商品
 */
class Baod extends Common {

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

        $this->map[] = ['prod_zone','=',1];
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
        // foreach ($list as $k => $v) {
        //     $list[$k]['prod_pic'] = explode(',',$v['prod_pic'])[0];
        // } 
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
            if(!$data['prod_name'] || !$data['prod_price']){
                 return ['code'=>0,'msg'=>'非法操作'];
            }

            //验证器
            // $validate = validate('Prod');
            // $res = $validate->scene('add')->check($data);
            // if (!$res) {
            //     $this->error($validate->getError());
            // }

            // if($data['cate_id']==0){
            //     return ['code'=>0,'msg'=>'请选择产品分类'];
            // }
            
            $data['prod_zone'] = 1;
            $rel = model('StoProd')->tianjia($data);
            return $rel;
        }
        $cate = model('StoCate')->where('cate_pid', 0)->order('cate_sort desc')->select();
        foreach ($cate as $k => $v) {
            $cate[$k]['son'] = model('StoCate')->where('cate_pid', $v['id'])->select();
        }
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
            $rel = model('StoProd')->destroy($id);
            if ($rel) {
                $this->success('删除成功');
            } else {
                $this->error('请联系网站管理员');
            }
        } else {
            $this->error('数据不存在');
        }
    }

    // 订单列表
    public function order() {
        if (is_post()) {

            $rst = model('Order')->xiugai([input('post.key') => input('post.value')], ['id' => input('post.id')]);
            return $rst;
        }
        if (input('get.keywords')) {
            $us_id = model("User")->where('us_account|us_tel', input('get.keywords'))->value('id');
            if ($us_id) {
                $array = model('StoOrder')->where('us_id',$us_id)->field('order_number')->select()->toArray();
                $arr = array_column($array,'order_number');
                $this->map[] = ['us_id', 'in', $arr];
            }
        }
        if (input('get.prod_name')) {
            $ord_number = Db::name('ord_det')->where('prod_name','like','%'.input('get.prod_name').'%')->field('ord_number')->select();
            if($ord_number){
                $arr = array_column($ord_number,'ord_number');
                $this->map[] = ['ord_number', 'in', $arr];
            }else{
                $this->map[] = ['ord_number','=',0];
            }
        }

        // $this->map[] = ['prod_zone','=',0];
        if (input('get.status') != "") {
            $this->map[] = ['ord_status', '=', input('get.status')];
        }

        if (input('get.mer_name') != "") {
            $this->map[] = ['mer_id', 'like', '%'.input('get.mer_name').'%'];
        }

        if (input('get.order_number') != "") {
            $this->map[] = ['ord_number', '=', input('get.order_number')];
        }
        if (input('get.start')) {
            $this->map[] = ['ord_add_time', '>=', input('get.start')];
        }
        if (input('get.end')) {
            $this->map[] = ['ord_add_time', '<=', input('get.end')];
        }
        
        if (input('get.a') == 1) {
            $list = model("StoOrderDetail")->with('order')->where($this->map)->select();
            // $url = action('Excel/user', ['list' => $list]);
            $bb = env('ROOT_PATH') . "public\order.xlsx";
            if (file_exists($bb)) {
                $aa = unlink($bb);
            }
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            $sheet->setCellValue('A1', '订单编号')
                ->setCellValue('B1', '客户姓名')
                ->setCellValue('C1', '店铺')
                ->setCellValue('D1', '产品')
                ->setCellValue('E1', '产品类型')
                ->setCellValue('F1', '总价')
                ->setCellValue('G1', '单价')
                ->setCellValue('H1', '数量')
                ->setCellValue('I1', '状态')
                ->setCellValue('J1', '添加时间');
            $i = 2;
            foreach ($list as $k => $v) {
                $sheet->setCellValue('A' . $i, $v['order_number'])
                    ->setCellValue('B' . $i, $v->order->user['us_account'])
                    ->setCellValue('C' . $i, $v['mer_text'])
                    ->setCellValue('D' . $i, $v['prod_name'])
                    ->setCellValue('E' . $i, $v['zone_text'])
                    ->setCellValue('F' . $i, $v['order_money'])
                    ->setCellValue('G' . $i, $v['prod_price'])
                    ->setCellValue('H' . $i, $v['prod_num'])
                    ->setCellValue('I' . $i, $v['status_text'])
                    ->setCellValue('J' . $i, $v['detail_add_time']);
                $i++;
            }
            
            $writer = new Xlsx($spreadsheet);
            $writer->save('order.xlsx');
            return "http://" . $_SERVER['HTTP_HOST'] . "/order.xlsx";
        }
        $list = model('OrdBao')->chaxun($this->map, $this->order, $this->size);
        $this->assign(array(
            'list' => $list,
        ));
        return $this->fetch();
    }


    public function detail() {
        
        $id = input('id');
        $info = model('OrdBao')->with('user')->where('id',$id)->find();
        if (is_post()) {
            $da  = input('post.');
            if($info['detail_status']<1 || $info['detail_status']>3){
                return ['code'=>0,'msg'=>'该订单状态不支持发货'];
            }
            $da['detail_status'] = 2;
            $da['detail_delive_time'] = date('Y-m-d H:i:s');
            $res = model("StoOrderDetail")->update($da);
            // if($res && $info['mer_id']){
            //  $prod = model("StoProd")->get($info['prod_id']);
            //  $mer = model('StoMer')->get($info['mer_id']);
            //  $num = $prod['prod_price']*$info['prod_num']*cache('setting')['huo_calcu']/100;
            //  model("ProWal")->tianjia($mer['us_id'],$num,15); 
            // }
            return ['code'=>1,'msg'=>'成功'];
        }
        $mer = Db::name('sto_mer')->field('id,mer_name')->select();
        // $id = input('get.id');
        // $info = model('StoOrderDetail')->detail(['id'=>$id]);
        $this->assign(array(
            'info' => $info,
            'mer' => $mer,
        ));
        return $this->fetch();
    }

    public function finish(){
        if(is_post()){
            $id = input('post.id');
            $info = model('OrdBao')->where('id',$id)->find();
            if($info['bao_status']!=1){
                $this->error('该订单不是待收货状态');
            }
            if(input('mer_id') == ""){
                $this->error('请传入提货门店');
            }
            // $time = unixtime('day',-10);
            // $ten = date('Y-m-d H:i:s',$time);
            
            // if($info['detail_status']!=2 || $info['detail_delive_time']>$ten ){
            //     return ['code'=>0,'msg'=>'该订单不是待收货状态或发货时间小于10天'];
            // }
            $data = array(
                'bao_finish_time' => date('Y-m-d H:i:s'),
                'bao_status' => 2,
                'mer_id' => input('post.mer_id'),
            );
            $rel = model('OrdBao')->where('id',$id)->update($data);
            if ($rel) {
                $this->success('确定收货成功');
            } else {
                $this->error('确定收货失败');
            }
        }
    }   

    public function ord_del(){
        if (input('post.id')) {
            $id = input('post.id');
        } else {
            $this->error('id不存在');
        }
        $info = model('StoOrderDetail')->get($id);
        if ($info) {
            $rel = model('StoOrderDetail')->destroy($id);
            if ($rel) {
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
