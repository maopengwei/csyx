<?php
namespace app\common\validate;

use think\Validate;

/**
 * 支付验证器
 */
class pay extends Validate {
	protected $rule = [
		'us_id' => 'require',
		'num' => 'require|number|gt:0',
		'relevance' => 'require',
	];
	protected $field = [
		'us_id' => '用户',
		'num' => '金额',
		'relevance' => '关联订单',
	];
	protected $message = [
		'us_id.require' => '请传入用户信息',
		'num.require' => '请传入金额', 
		'num.number' => '请传入金额', 
		'num.gt' => '请传入金额', 
		'relevance.require' => '请传入订单信息', 
	];
}
