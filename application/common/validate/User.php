<?php
namespace app\common\validate;

use think\Validate;

/**
 * 添加管理员验证器
 */
class User extends Validate {
	protected $rule = [
		'p_vip_account' => 'require',
		'p_acc' => 'require',
		'p_tel' => 'require|regex:/^[1][34578][0-9]{9}$/',
		'a_acc' => 'require',
		'a_tel' => 'require|regex:/^[1][34578][0-9]{9}$/',
		'us_aid' => 'require',
		'us_pid' => 'require',
		'us_account'   => 'require|alphaNum',
		'us_real_name' => 'require',
		'us_tel'	   => 'require|regex:/^[1][34578][0-9]{9}$/',
		'us_pwd' 	   => 'require',
		'us_safe_pwd'  => 'require',
		'us_qu' 	   => 'require',
		'us_type' 	   => 'require',
		'old_pwd' 	   => 'require',
		'sode' 		   => 'require',
		'us_addr_addr'    => 'require',
		'us_addr_tel'     => 'require',
		'us_addr_person'  => 'require',
	];
	protected $field = [
		'p_vip_account' => '推荐码',
		'p_acc' => '父账号',
		'p_tel' => '父账号手机号',
		'a_acc' => '节点人账号',
		'a_tel' => '节点人手机号',
		'us_aid'       => '节点人',
		'us_pid'       => '父账号',
		'us_account'   => '帐户名',
		'us_real_name' => '用户真实姓名',
		'us_tel'       => '手机号',
		'us_pwd'       => '用户登录密码',
		'us_safe_pwd'  => '用户安全密码',
		'us_qu'        => '区',
		'old_pwd'      => '原密码',
		'sode'		   => '短信验证码',
		'us_addr_addr'    => '收货地址',
		'us_addr_tel'     => '收货电话',
		'us_addr_person'  => '收货人',
	];
	protected $message = [
		'us_tel.regex' => '请填写正确的手机号',
		'is_coin.require' => '请选择是否使用购物币',
		'is_reservation.require' => '请选择是否使用预定',
		'is_courier.require' => '请选择是否需要配送',
	];
	protected $scene = [
		'add' => ['p_account','us_account','us_real_name', 'us_tel', 'us_pwd','us_safe_pwd'], //添加用户
		'homeadd' => ['us_account','us_tel','sode','us_pwd','us_safe_pwd'], //添加用户
		'addr' => ['us_addr_addr','us_addr_tel','us_addr_person'],
		'editUser' => ['us_real_name', 'us_tel'], //修改用户
		'pass' => ['us_pwd', 'us_tel','sode'], //修改密码
		'safe' => ['us_safe_pwd','us_tel','sode'], //忘记密码
		'tel' => ['us_tel','sode','us_pwd'], //修改手机号
	];

}
