<?php

namespace app\index\Controller;

use app\common\controller\Api;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;
use think\Facade\Log as lllog;
use func\PassN;
use think\Db;
class Wechat extends Api
{
    protected $cc = [
        // 'appid' => 'wxb3fxxxxxxxxxxx', // APP APPID
        'app_id' => 'wxbefe55cc82802a4c', // 公众号 APPID
        // 'miniapp_id' => 'wxb3fxxxxxxxxxxx', // 小程序 APPID
        'mch_id' => '1508989651',
        'key' => 'qwertyuiopLKJHGFDSA654321ZXCvbnm',
        // 'app_secret' => 'e5cad3753be560f79027e2f0a4cafedf',
        'notify_url' => 'http://als.jugekeji.cn/index/wechat/notify',
        'return_url' => 'http://als.jugekeji.cn/index/user/index',
        'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
        'cert_key' => './cert/apiclient_key.pem',// optional，退款等情况时用到
        'log' => [ // optional
            'file' => './logs/wechat.log',
            'level' => 'debug'
        ],
        // 'mode' => 'dev', // optional, dev/hk;当为 `hk` 时，为香港 gateway。
    ];

    public function bb(){
        return $this->fetch();
    }

    public function index(){
        if(is_post()){
            $d = input('post.');
            /*
            
                

             */
            $validate = validate('Pay');
            $res = $validate->check($d);
            if (!$res) {
                $this->e_msg($validate->getError());
            }


            $us_id = $d['us_id'];
            $orderid = "chao" . date("YmdHis") . rand(100, 999);
            $num = $d['num'];
            $type = $d['type'];
            $relevance = $d['relevance'];



            $uu = Db::name('user')->where('id',$us_id)->field('id,us_account')->find();
            if(!$uu){
                $this->error('该用户不存在');
            }

            // $us_id = 1;
            // $num = 100;
            // $relevance = 1;

            
            $rel = model('WecpayPay')->tianjia($d['us_id'], $orderid, $d['num'], $d['type'], $d['relevance']);
            if($rel){
                $rrr = model('WecpayPay')->back_success($orderid);
                $this->s_msg('支付成功');
            }

            $order = [
                'out_trade_no' => $orderid,
                // 'total_fee' => 1, // **单位：分**
                'total_fee' => $num*100, // **单位：分**
                'body' => '移动网页支付',
            ];
            $wechat = Pay::wechat($this->cc);
        }else{
            $this->e_msg('get');
        }

        

        // $this->msg($wechat->wap($order));

        // $result = Pay::wechat($this->config)->wap($order);

        // $string = json_encode($result);
        // $this->assign('order',$string);
        // return $this->fetch();
        // // $wechat = Pay::wechat($this->config);   
        return $wechat->wap($order)->send();


        // $order = [
        //     'out_trade_no' => $orderid,
        //     // 'total_fee' => 1, // **单位：分**
        //     'total_fee' => $num*100, // **单位：分**
        //     'body' => '移动网页支付',
        // ];
        // $wechat = Pay::wechat($this->config);

        // $this->msg($wechat->wap($order));
        // return $wechat->wap($order)->send();

        // if ($rel) {
        //  $rst = model('WecpayPay')->back_success($orderid);
        //  if ($rst) {
        //      $this->success('支付成功', '/index/user/index');
        //  }
        // } else {
        //  $this->error('失败');
        // }
        if(session('openid')){
            $order = [
                'out_trade_no' => $orderid,
                'body' => '公众号支付',
                'total_fee' => $num*100,
                'openid' => session('openid'),
            ];
            $result = Pay::wechat($this->config)->mp($order);

            $string = json_encode($result);
            
            $this->assign('order',$string);
            return $this->fetch();
        }else{ 
            // halt(123);
            $order = [
                'out_trade_no' => $orderid,
                // 'total_fee' => 1, // **单位：分**
                'total_fee' => $num*100, // **单位：分**
                'body' => '移动网页支付',
            ];
            $wechat = Pay::wechat($this->config);
            $result = Pay::wechat($this->config)->wap($order);
            $string = json_encode($result);
            
            $this->assign('order',$string);
            return $this->fetch();
            return $wechat->wap($order)->send();
        }


    }

    public function index1()
    {
    	// if(is_post()){
		
		$d = input('post.');
		// halt($d);
		// $validate = validate('Pay');
  //       $res = $validate->scene('pay')->check($d);
  //       if (!$res) {
  //           $this->e_msg($validate->getError());
  //       }
  		$us_id = 1;
  		$orderid = "sp" . date("YmdHis") . rand(100, 999);
  		$num = 100;
  		$type = 1;
        $relevance = 1;
        // halt($d);

        $uu = Db::name('user')->where('id',$us_id)->field('id,us_account,us_safe_pwd')->find();
        if(!$uu){
        	$this->error('该用户不存在');
        }
        // halt($d['us_safe_pwd']);

        // if(PassN::mine_encrypt($d['us_safe_pwd']) != $uu['us_safe_pwd']){
        //     $this->e_msg('安全密码不正确');
        // }
        // $rel = model('WechatPay')->tianjia($us_id, $orderid, $num, $type, $relevance);

         $order = [
            'out_trade_no' => $orderid,
            // 'total_fee' => 1, // **单位：分**
            'total_fee' => $num*100, // **单位：分**
            'body' => '移动网页支付',
        ];
        $wechat = Pay::wechat($this->config);   
        return $wechat->wap($order)->send();


        if ($rel) {
     		$rst = model('WechatPay')->back_success($orderid);
     		if ($rst) {
         		$this->s_msg('支付成功');
     		}
        }
    	
    }


    public function notify()
    {
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // 是的，验签就这么简单！
            $arr = $data->all();
            lllog::write($arr,'notice');
            if($arr['result_code']=='SUCCESS'){
                 model('WecpayPay')->back_success($arr['out_trade_no']);
            }
            Log::debug('Wechat notify', $data->all());
        } catch (Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// laravel 框架中请直接 `return $pay->success()`
    }
    
}