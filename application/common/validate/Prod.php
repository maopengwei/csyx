<?php
namespace app\common\validate;

use think\Validate;

/**
 * 商品验证器
 */
class Prod extends Validate {
	protected $rule = [
		'us_id'    		=> 'require',
		'mer_name' 		=> 'require',
		'mer_pic'  		=> 'require',
		'mer_gift'  	=> 'require',
		'us_account'  	=> 'require',
		'us_tel'  		=> 'require',

		'mer_id'     => 'require',
		'prod_id'    => 'require',
		'prod_num'    => 'require',
		'us_safe_pwd'    => 'require',
		'prod_id'    => 'require',

		'prod_name'  => 'require',
		'prod_pic'   => 'require',
		'prod_logo'   => 'require',
		'prod_price' => 'require',
		'prod_price_yuan' => 'require',
		'cate_id' 	 => 'require',
	];
	protected $field = [
		'us_id' 		=> '用户',
		'mer_name' 		=> '商铺名称',
		'mer_pic' 		=> '商铺主图',
		'mer_gift' 		=> '商铺种类',
		'us_account' 	=> '用户账号',
		'us_tel' 		=> '用户手机号',

		'prod_id'    	=> '商品',
		'prod_num'    	=> '商品数量',
		'us_safe_pwd'   => '支付密码',
		'addr_id'    	=> '地址',

		'mer_id' 		=> '商铺',
		'prod_name' 	=> '产品名称',
		'prod_pic' 		=> '商品主图',
		'prod_logo' 	=> '详情图',
		'prod_price' 	=> 'vip价格',
		'prod_price_yuan' 	=> '普通价格',
		'cate_id' 		=> '分类',
		'arrid'  		=> '产品',
	];
	protected $message = [
		
	];
	protected $scene = [
		'add' => ['prod_name', 'prod_pic', 'prod_price','prod_price_yuan'], //添加产品
		'edit' => ['prod_name', 'prod_logo', 'prod_price'], //编辑产品
		'order' => ['us_safe_pwd','prod_id','prod_num','addr_id'],
		'cartorder' => ['us_safe_pwd','arrid','addr_id'], 
	];

}
