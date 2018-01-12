<?php
 /*
 ** 推送服务接口
 */
class PushController extends Yaf_Controller_Abstract {

	// 单个推送
	public function singleAction() {
		if (!$this->_checkAdmin()) {
			return false;
		}

		$cid = $this->getRequest()->getPost("cid","");
		$msg = $this->getRequest()->getPost("msg","");

		if (!$cid || !$msg) {
			echo json_encode(array(
				"errno"=>-7002,
				"errmsg"=>"请输入推送用户的设备ID与要推送的内容"
			));
			return false;
		}

		// setup model
		$model = new PushModel();
		
		if ($model->single($cid, $msg)) {
			echo json_encode(array(
				"errno"=>0,
				"errmsg"=>""
			));		
		} else {
			echo json_encode(array(
				"errno"=>$model->errno,
				"errmsg"=>$model->errmsg
			));
		}
	
		return true;

	}
	
	// 全部推送
	public function toallAction() {
		if (!$this->_checkAdmin()) {
			return false;
		}

		$msg = $this->getRequest()->getPost("msg","");

		if (!$msg) {
			echo json_encode(array(
				"errno"=>-7002,
				"errmsg"=>"请输入推送用户的设备ID与要推送的内容"
			));
			return false;
		}

		// setup model
		$model = new PushModel();

		if ($model->toAll($msg)) {
			echo json_encode(array(
				"errno"=>0,
				"errmsg"=>""
			));
		} else {
			echo json_encode(array(
				"errno"=>$model->errno,
				"errmsg"=>$model->errmsg
			));
		}
	
		return true;

	}

	// 当前用户检测
	private function _isAdmin() {
		return true;
	}

	private function _checkAdmin() {
		if (!$this->_isAdmin()) {
			echo json_encode(array(
				"errno"=>-2000,
				"errmsg"=>"你不能执行该操作"
			));	
			return false;
		} else {
			$submit = $this->getRequest()->getQuery("submit", "0");
			if ($submit != "1") {
				echo json_encode(array(
					"errno"=>-2001,
					"errmsg"=>"提交渠道不正常"
				));
				return false;
			}
		}
		return true;
	}
	
}
