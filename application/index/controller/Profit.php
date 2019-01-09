<?php
namespace app\index\controller;

class Profit extends Base 
{
    public function wal(){
        $this->map[] = ['us_id', '=', $this->user['id']];
        if(input('size')){
            $this->size = input('size');
        }
        if(input('type')==0){
            $this->map[] = ['wal_type','in',[4,5,6]; 
        }elseif(input('type')==1){
            $this->map[] = ['wal_type','=',8]; 
        }elseif(input('type')==2){
            $this->map[] = ['wal_type','=',7]; 
        }elseif(input('type')==3){
            $this->map[] = ['wal_type','in',[1,2,3]];
        }

        $list = model('ProWal')->chaxun($this->map, $this->order, $this->size);
        $arr = [
            0 => $list,
            1 => $this->user['us_account'],
            2 => $this->user['us_vip_account'],
        ];
        $this->msg($arr);

    }
   

    public function ti(){
        if(is_post()){
            $arr = [
                'yue' => $this->user['us_wal'],
                'aready' => 0,
                'ting' => 0, 
            ];
            $this->msg($arr);
        }
    }

    public function tx(){
        $d = input('post.');
        
        $validate = validate('Profit');
        $res = $validate->scene('tx')->check($d);
        if (!$res) {
            $this->e_msg($validate->getError());
        }

        if($this->user['us_wal']<=0){
            $this->e_msg('您的余额为0');
        }
        $d['us_id'] = $this->user['id'];
        $d['tx_num'] = $this->user['us_wal'];
        // halt($d); 
        $rel = model("ProTixian")->tianjia($d);
        $this->msg($rel);
    }

    
    public function tx_list(){
        $this->map[] = ['us_id','=',$this->user['id']];
        if(input('size')){
            $this->size = input('size');
        }
        $list = model('ProTixian')->chaxun($this->map, $this->order, $this->size);
        $this->msg($list);
    }



    // 茶币转账
    // public function trans(){
    //     if(is_post()){
    //         $d = input('post.');

    //         $validate = validate('Profit');
    //         $res = $validate->scene('trans')->check($d);
    //         if (!$res) {
    //             $this->e_msg($validate->getError());
    //         }

    //         if($this->user['us_account']==$d['tr_account']){
    //             $this->e_msg('您不能转给自己');
    //         }

    //         if(mine_encrypt($d['us_safe_pwd']) != $this->user['us_safe_pwd']){
    //             $this->e_msg('安全密码不正确');
    //         }else{ 
    //             unset($d['us_safe_pwd']);
    //         }
           
    //         if($this->user['us_wal']<$d['tr_num']){
    //             $this->e_msg('茶币不足');
    //         }
    //         $rel = model('ProTransfer')->tianjia($d,$this->user['id']);
    //         $this->msg($rel);

    //     }else{
            
    //         $this->map[] = ['us_id|us_to_id', '=', $this->user['id']];
    //         $list = model('ProTransfer')->chaxun($this->map, $this->order, $this->size);
    //         foreach ($list as $k => $v) {
    //             if($v['us_id']==$this->user['id']){
    //                 $list[$k]['tr_num'] = '-'.$v['tr_num'];
    //             }
    //             $list[$k]['us_account'] = $this->user['us_account'];
    //         }
    //         $this->msg($list);
        
    //     }
    // }
    //转换
    // public function convert(){
    //     if(is_post()){
    //         $d = input('post.');
    //         $validate = validate('Profit');
    //         $res = $validate->scene('convert')->check($d);
    //         if (!$res) {
    //             $this->e_msg($validate->getError());
    //         }
            
    //         if(mine_encrypt($d['us_safe_pwd']) != $this->user['us_safe_pwd']){
    //             $this->e_msg('安全密码不正确');
    //         }else{
    //             unset($d['us_safe_pwd']);
    //         }

    //         if($this->user['us_msc']<$d['convert_num']){
    //             $this->e_msg('您的奖励不足');
    //         }
          
    //         $rel = model("ProConvert")->tianjia($this->user['id'],$d['convert_num']);
    //         $this->s_msg('转换成功');
    //     }else{
    //         $this->map[] = ['us_id','=',$this->user['id']];
    //         if(input('size')){
    //             $this->size = input('size');
    //         }
    //         $list = model('ProConvert')->chaxun($this->map, $this->order, $this->size);
    //         $this->msg($list);
    //     }
    // }
    
}
