<?php
 /*
 ** 微信支付功能封装
 */
class WxpayController extends Yaf_Controller_Abstract {

	public function indexAction() {}
	// 创建账单
	public function createBillAction() {
		$itemid = $this->getRequest()->getQuery('itemid', '');
		if (!$itemid) {
			echo json_encode(array('errno'=>'-6001', 'errmsg'=>'请传递商品ID'));
			return false;
		}
		if ($this->_checkAdmin()) {
			$model = new WxpayModel();
			if ($data = $model->createbill($itemid, $_SESSION['user_id'])) {
				echo json_encode(array(
					'errno'=>0,
					'errmsg'=>'',
					'data'=>$data
				));		
			} else {
				echo json_encode(array(
					'errno'=>$model->errno,
					'errmsg'=>$model->errmsg
				));		
			}
		}
		return true;
	}
	// 创建二维码
	public function qrcodeAction() {}
	// 支付回调
	public function callbackAcion() {}
	
	// 当前用户检测
	private function _checkAdmin() {
		session_start();
		if (
			!isset($_SESSION['user_token_time']) || 
			!isset($_SESSION['user_token']) ||
			!isset($_SESSION['user_id']) ||
			md5('tjhzs-session-'.$_SESSION['user_token_time'].$_SESSION['user_id']) != $_SESSION['user_token']		
		) {
			echo json_encode(array('errno'=>-6002, 'errmsg' => '请先登录后操作'));
			return false;
		}
		return true;
	}
	
}
