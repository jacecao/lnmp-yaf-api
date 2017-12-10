<?php

class UserController extends Yaf_Controller_Abstract {
	
	public function indexAction() {
		return $this->loginAction();		
	}

	public function loginAction() {
		echo "hello";
		return FALSE;			
	}

	public function registerAction() {
		// 获取参数
		$uname = $this->getRequest()->getPost("username", false);
		$pwd = $this->getRequest()->getPost("pwd", false);
		
		if (!$uname || !$pwd) {
			echo json_encode(array(
				"errno"=>-1002,
				"errmsg"=>"用户名与密码必须传递"
			));
			return FALSE;
		}

		// 调用数据模块，做登录验证
		$model = new UserModel();
		if ($model->register( trim($uname), trim($pwd) )) {
			echo json_encode(array(
				"errno"=>0,
				"errmsg"=>"",
				"data"=>array("name"=>$uname)
			));
		} else {
			echo json_encode(array(
				"errno"=>$model->errno,
				"errmsg"=>$model->errmsg,
			));		
		}
		return FALSE;
	}
}

