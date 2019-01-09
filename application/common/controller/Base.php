<?php
namespace app\common\controller;

use think\Controller;

class Base extends Controller {
	protected $order;
	protected $size;
	protected $map;
	public function initialize() {
		
		parent::initialize();
		// !cache('setting') && cache('setting',model('SysConfig')->getInfo());
		// !cache('calcu') && cache('calcu',db('calcu')->select());
		// !cache('jing') && cache('jing',db('jing')->select());
		cache('setting',model('SysConfig')->getInfo());
		cache('level',db('sys_level')->select());
		$this->order = 'id desc';
		$this->size = '20';
		$this->map = [];
	
	}
	
	public function _empty($name) {
		$request = request();
		$file = env('app_path') . $request->module() . '/view/' . lcfirst($request->controller()) . "/" . $name . '.' . ltrim(config('template.view_suffix'), '.');
		if (file_exists($file)) {
			return $this->fetch($name);
		} else {
			$this->redirect('Index/index');
		}
	}
	//网站维护
    public function system() {
        // if (cache('setting')['sys_status'] == 0) {
        //     $this->error('网站维护中');
        // }
    }
	public function is_weixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {

            return true;
        }
        return false;
    }

    


}
