<?php
/**
 * Created by Maopw
 * Todo: 升级会员
 * Date: 2018年12月21日
 * 
 */

namespace app\common\logic;

use think\Db;
use think\facade\Config;

class Sms 
{
    
    public static function Send($arr){
        $mobile = $arr['us_tel'];
        $type = $arr['type'];

        if(Db::name('user')->where('us_tel', $mobile)->count()){
            if ('reg' === $type) {
                return x_code('该手机号已注册');
            }
        }else{
            // 忘记密码/登陆  获取验证码
            if ('fg' == $type) {
                return x_code('该手机号未注册账户');
            }
        }
        if (cache($mobile . 'code')) {
            return x_code('每次发送间隔120秒');
        }else{
            cache($mobile . 'code', 123456,120);
            return x_code('发送成功,现在的验证码是123456',1);
        }
        halt(123);
        $random = mt_rand(100000, 999999);
        $xxx = self::noteCode($mobile, $random);
        halt($xxx);
        $rel = object_array($xxx);
        if ($rel['returnstatus'] == "Faild") {
            $this->e_msg($rel['message']);
        } else {
            cache($mobile . 'code', $random,120);
            $this->s_msg('发送成功');
        }
        halt($arr);
    }


    public static function noteCode($mobile, $content) {
        header('Content-Type:text/html;charset=utf8');
        $sms = config('sms');
        $sms['password'] = ucfirst(md5($sms['password']));
        $sms['content'] = $sms['content'] . $content;
        // $sms['content'] = urlencode($sms['content']);
        $sms['mobile'] = $mobile;
        $query_str = http_build_query($sms);
        $gateway = "http://114.113.154.5/sms.aspx?action=send&" . $query_str;
        // dump($gateway);
        // echo "<br />";
        // $gateway = "http://114.113.154.5/sms.aspx?action=send&userid={$sms['userid']}&account={$sms['account']}&password={$sms['password']}&mobile={$mobile}&content={$sms['content']}&sendTime=";
        // dump($gateway);
        // $gateway = "= "http://114.113.154.5/sms.aspx?action=send&".$q".$query_str;
        // $result = file_get_contents($gateway);
        $url = preg_replace("/ /", "%20", $gateway);
        $result = file_get_contents($url);
        return $xml = simplexml_load_string($result);
        //  $this->object_array($xml);
    }



    /**
     * 地址添加和修改
     * author fengkl
     * time 2018年6月4日 15:49:11
     * @return mixed
     * code为1为修改
     */
    public function saveAddr($data,$code = '')
    {
        //var_dump($data);exit();
        $validate = validate('Verify');
        $rel = array();
        $rst = $validate->scene('addAddr')->check($data);
        if (!$rst) {
            $rel['code'] = 0;
            $rel['msg'] = $validate->getError();
            return $rel;
        }       
        if($code == 1){
            //修改操作              
            $map['id'] = $data['id'];
            unset($data['id']);   
            $data['addr_code'] = implode(',', $data['addr_code']);
            $data['addr_addr'] = $data['province'].$data['city'].$data['area'];
            $rell = model('UserAddr')->updateInfo($map,$data);
            if($rell){
                $rel['code'] = 1;
                $rel['msg'] = '修改成功！';
            }else{
                $rel['code'] = 0;
                $rel['msg'] = '您没有作出修改！';
            }
            return $rel;
        }
        $data['add_time'] = date('Y-m-d H:i:s');
        $data['addr_default'] = 1;
        $data['addr_addr'] = $data['province'].$data['city'].$data['area'];
        $addrmodel = model('UserAddr');
        $de_rel = $addrmodel->where('addr_default',1)->setDec('addr_default',1);
        //添加操作 
        //var_dump($data);exit();
        $data['addr_code'] = implode(',', $data['addr_code']);     
        $rell = $addrmodel->addInfo($data);
        if($rell){
            $rel['code'] = 1;
            $rel['msg'] = '添加成功！';
        }else{
            $rel['code'] = 0;
            $rel['msg'] = '添加失败！';
        }
        return $rel;
    }


}