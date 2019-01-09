<?php
namespace app\common\validate;

use think\Validate;

/**
 * 商城验证器
 */
class Apply extends Validate {
    
	protected $rule = [
		'apply_card_front' => 'require',  //身份证前
        'apply_card_back' => 'require', //身份证后
        'apply_trad' => 'require',           //营业执照
        'apply_ying' => 'require',           //手持身份证照
        'apply_name' => 'require',           //店铺名称
        'apply_tel' => 'require',             //手机号
        'apply_person' => 'require',       //人
        'apply_addr' => 'require',           //地址
	];
	protected $field = [
		'apply_card_front' => '身份证正面照',  //身份证前
        'apply_card_back' => '身份证反面照', //身份证后
        'apply_trad' => '营业执照',           //营业执照
        'apply_ying' => '手持身份证照',           //手持身份证照
        'apply_name' => '店铺名称',           //店铺名称
        'apply_tel' => '手机号',             //手机号
        'apply_person' => '申请人',       //人
        'apply_addr' => '店铺地址',           //地址
	];
	protected $message = [
		
	];
	protected $scene = [
		'apply' => ['apply_card_front','apply_card_back', 'apply_trad','apply_ying','apply_name','apply_tel','apply_person','apply_addr'],
	];

}
