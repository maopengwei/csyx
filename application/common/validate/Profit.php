<?php
namespace app\common\validate;

use think\Validate;

/**
 * 金额验证器
 */
class Profit extends Validate {
	protected $rule = [
        'us_tel'    => 'require|mobile',
        'tr_num'    => 'require|number|gt:0',
        'convert_num'    => 'require|number|gt:0',
		'sode'    => 'require',
		'tx_us_name' => 'require',
         'tx_us_tel' => 'require',
         'bank_person' => 'require',
         'bank_account' => 'require',
         'bank_addr' => 'require',
		'us_safe_pwd' => 'require',
	];
	protected $field = [
         'tx_us_name' => '姓名',
         'tx_us_tel' => '手机号',
         'bank_person' => '持卡人',
         'bank_account' => '银行账号',
         'bank_addr' => '开户行',

        'us_tel' => '手机号',
		'tr_num' => '转让数量',
		'trans_account' => '对方账号',
        'convert_num' => '转换数量',
		'sode' => '短信验证码',
		
	];
	protected $message = [
		
	];
	protected $scene = [
		'trans' => ['tr_account','tr_num', 'us_safe_pwd'],       //转账
		'convert' => ['convert_num', 'us_safe_pwd'], //转换
		'tx' => ['tx_us_name','tx_us_tel','sode','bank_person','bank_account','bank_addr'], //提现
	];

}
