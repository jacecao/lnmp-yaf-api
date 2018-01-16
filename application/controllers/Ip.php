<?php
 /*
 ** IP地址查询
 */
class IpController extends Yaf_Controller_Abstract {

	public function indexAction() {
		$this->getAction();	
	}
	
	// 发送邮件
	public function getAction() {
	
		$ip = $this->getRequest()->getQuery("ip", "");
		
		// IP地址判断
		if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
			echo json_encode(array(
				"errno"=>-5001,
				"errmsg"=>"请传正确的IP地址"
			));
			return false;
		}
	
		// 调用MODEL,查询IP归属地
		$model = new IpModel();
		
		if ( $data = $model->get( trim($ip) ) ) {
			
			echo json_encode(array(
				"errno"=>0,
				"errmsg"=>$data
			));

		} else {
			echo json_encode(array(
				"errno"=>$model->errno,
				"errmsg"=>$model->errmsg
			));		
		}

		return true;
	}

	
}
