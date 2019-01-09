<?php
namespace app\common\validate;

use think\Validate;

/**
 * 添加total验证器
 */
class Total extends Validate {
	protected $rule = [
		'us_tel' => 'require|regex:/^[1][345678][0-9]{9}$/',
		'type' => 'require',
		// 'p_tel' => 'require|regex:/^[1][34578][0-9]{9}$/',
	];
	protected $field = [
		'us_tel' => '手机号',
		'type' => '短信类型',
	];
	protected $message = [
		'us_tel.regex' => '请填写正确的手机号',
		'type.require' => '请选择是短信类型',
	];
	protected $scene = [
		'sms' => ['us_tel','type'], //添加用户
	];
}
