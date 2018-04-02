<?php
 /*
 ** 微信支付功能封装
 */
// 引入二维码生成模块
$qrcodeLibPath = dirname(__FILE__).'/../library/ThirdParty/';
include_once($qrcodeLibPath.'Qrcode.php');

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
	public function qrcodeAction() {
		$bill_id = $this->getRequest()->getQuery('billid', '');
		if (!$bill_id) {
			echo json_encode(array(
				'errno'=>-6008,
				'errmsg'=>'请提交正确的订单ID'
			));
			return FALSE;
		}
		// 检查用户状态
		if ($this->_checkAdmin()) {
			// 调用Model
			$model = new WxpayModel();
			if ($data = $model->qrcode($bill_id)) {
				// 输出二维码
				// $data 是微信服务返回的支付地址
				QRcode::png($data);
			} else {
				echo json_encode(array(
					'errno' => $model->errno,
					'errmsg' => $model->errmsg
				));
			}
			return true;
		}
	}
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
