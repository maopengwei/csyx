<?php
namespace app\mer\controller;

/**
 * @todo 首页操作
 */
class Index extends Common {
	// ------------------------------------------------------------------------
	public function index() {
		return $this->fetch();
	}

	// ------------------------------------------------------------------------
	public function welcome() {
		// 获取平台账户详情
		// 
		/*dump(session('mer_id'));
		dump(session('mer_us_id'));
		dump($this->mer);*/

 		$pd_count = model("StoMerProd")->where('mer_id',session('mer_id'))->count();
		$order_today = model("Ord")->where('mer_id',session('mer_id'))->whereTime('ord_add_time', 'today')->count();
		$order_jine = model("Ord")->where('mer_id',session('mer_id'))->whereTime('ord_add_time', 'today')->sum('ord_money');

		$order_count = model("Ord")->where('mer_id',session('mer_id'))->count();
		$order_total = model("Ord")->where('mer_id',session('mer_id'))->sum('ord_money');

		$this->assign(array(
			
			'pd_count' => $pd_count,
			'order_today' => $order_today,
			'order_jine' => $order_jine,
			'order_count' => $order_count,
			'order_total' => $order_total,

		));
		return $this->fetch();
	}
	// ------------------------------------------------------------------------

}
