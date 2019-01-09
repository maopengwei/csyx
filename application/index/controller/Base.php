<?php
namespace app\index\controller;

use think\Facade\Request;
use think\Response;
use think\exception\HttpResponseException;
use app\common\controller\Api;
use mine\PassO;
/**
 * 需要登录基类
 */
class Base extends Api {
	public $user;
	public function initialize() {
		parent::initialize();
		/*获取头部信息*/
		$header = $this->request->header();
		$authToken = null;
		if (key_exists('authtoken', $header)) {
			$authToken = $header['authtoken'];
		}
		if ($authToken) {
            $authToken = explode(':', $authToken);
            $this->user = model('User')->where("us_tel", $authToken[0])->find();
		} else {
			$this->e_msg("token不存在");
        }
        
        if (empty($this->user)) {
			$this->e_msg("账号不存在");
		}

        if (!cache('setting')['web_status']) {
			$this->e_msg("网站维护");
        }

        $password = $this->user['us_pwd'];

        $dataStr = PassO::mine_jsdecrypt($authToken[1], $password);

        $dataStr = explode(':', $dataStr);

        if (empty($dataStr)) {
            $this->e_msg('no access');
        }
        // dump($_SERVE);
        // halt($dataStr);
        if ($dataStr[0] != $_SERVER['REQUEST_URI']) {
            $this->e_msg('账户信息不正确');
        }
	}
    
    // public function initialize(){
    //     parent::initialize();
    //     $this->user = model('User')->where("us_tel",13000000000)->find();
    // }
    

}
