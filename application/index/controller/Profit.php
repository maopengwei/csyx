<?php
namespace app\index\controller;

class Profit extends Base 
{
    public function wal(){
        $this->map[] = ['us_id', '=', $this->user['id']];
        if(input('size')){
            $this->size = input('size');
        }
        $type = input('type')?input('type'):0;
        $this->map[] =$this->ttype($type);
        $list = model('ProWal')->chaxun($this->map, $this->order, $this->size);
        $this->msg($list);
    }

    public function ming(){
        $arr = [
            'us_account' => $this->user['us_account'],
            'us_vip_account' => $this->user['us_vip_account'],
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
    public function ttype($type){

        switch (true) {
            case $type==0:
                $tt = ['wal_type','in',[4,5,6]];
                break;
            case $type==1:
                $tt = ['wal_type','=',8]; 
                break;
            case $type==2:
                 $tt = ['wal_type','=',7]; 
                break;
            case $type==3:
                $tt = ['wal_type','in',[1,2,3]];
                break;
            default:
                break;
        }
        return $tt;
    }
    
}
