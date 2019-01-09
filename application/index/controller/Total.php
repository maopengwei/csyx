<?php
namespace app\index\controller;
use think\facade\Config;
use alysms\Hell;
use app\common\controller\Api;
use app\common\logic\Sms;
class Total extends Api 
{

    //系统
    public function level(){
        $level = cache('level');
        $this->msg($level);
    }
    public function system(){

        $setting = cache('setting');
        $this->msg($setting);
    
    }
    //关于我们
    public function about(){
        $about = cache('setting')['web_about'];
        // $about = html_entity_decode($about);
        $this->msg($about);
    }


    /**
	 * 上传图片 base64
	 * @return [type] [description]
	 */
	public function uploads() {
		try {
			$rel = base64_upload(input('post.img'));
		} catch (\Exception $e) {
			$this->e_msg($e->getMessage());
		}
		if ($rel) {
            $arr = [
                'code'=>1,
                'msg' => "成功",
                'data' => $rel,
            ];
            $this->msg($arr);
		} else {
			$this->e_msg('失败');
        }
       
    }
    /**
     * 上传图片 文件对象
     * @return [type] [description]
     */
    public function upload() {
        $bb = env('ROOT_PATH');
        $file = request()->file('img');
        if($file){
            $info = $file->validate(['size' => '4096000'])
            ->move($bb . 'public/uploads/');
            if ($info) {
                $path = '/uploads/' . $info->getsavename();
                $path = str_replace('\\', '/', $path);
                $data = array(
                    'code' => 1,
                    'msg' => '上传成功',
                    'data' => $path,
                );
            } else {
                $data = array(
                    'msg' => $file->getError(),
                    'code' => 0,
                );
            }
            $this->msg($data);
        }else{
            $this->e_msg('请传入图片');
        }
        
    }
   
    public function send(){
        $r = input('post.');
        $validate = validate('Total');
        $res = $validate->scene('sms')->check($r);
        if (!$res) {
            $this->e_msg($validate->getError());
        }
        $rel = Sms::send($r);
        $this->msg($rel);
    }

}
