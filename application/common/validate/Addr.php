<?php
namespace app\common\validate;

use think\Validate;

/**
 * 商城验证器
 */
class Addr extends Validate {
    
	protected $rule = [
		'addr_name' => 'require',
		'addr_stree' => 'require',
		'addr_tel' => 'require',
		'addr_detail' => 'require',
		'addr_latitude' => 'require',
		'addr_longitude' => 'require',
	];
	protected $field = [
		'addr_name' => '收货人',
		'addr_stree' => '收货地址',
		'addr_tel' => '收货电话',
		'addr_detail' => '街道信息',
		'addr_latitude' => '经度',
		'addr_longitude' => '纬度',
	];
	protected $message = [
		'addr_latitude.require' => '请选择一个正确的收货地址',
		'addr_longitude.require' => '请选择一个正确的收货地址',
	];
	protected $scene = [
		'addr' => ['addr_name','addr_stree', 'addr_tel', 'addr_detail', 'addr_latitude', 'addr_longitude'], //添加地址
	];

}
