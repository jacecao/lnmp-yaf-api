<?php

class UserController extends Yaf_Controller_Abstract {
	
	public function indexAction() {
		return $this->loginAction();		
	}

	public function loginAction() {
		// 验证 注册提交submit字段
		$submit = $this->getRequest()->getQuery("submit", "0");
		if ($submit != "1") {
			echo json_encode(array(
			"errno"=>-1001,
			"errmsg"=>"请通过正确渠道提交"
			));
			return FALSE;
		}
		// 获取参数
		$uname = $this->getRequest()->getPost("uname", false);
		$pwd = $this->getRequest()->getPost("pwd", false);
		if (!$uname || !$pwd) {
			echo json_encode(array(
				"errno"=>-1002,
				"errmsg"=> "用户名和密码不能为空"
			));
			return FALSE;
		}
		
		// 调用model, 做登录验证
		$model = new UserModel();
		$uid = $model->login(trim($uname), trim($pwd));
		if ($uid) {
			
			// 储存回话信息
			session_start();
			$_SESSION['user_token'] = md5("tjhzs-session-".$_SERVER['REQUEST_TIME'].$uid);
			$_SESSION['user_token_time'] = $_SERVER['REQUEST_TIME'];
			$_SESSION['user_id'] = $uid;
			echo json_encode(array(
				"errno"=>0,
				"errmsg"=>"",
				"data"=>array("name"=>$uname)
			));
		} else {
			echo json_encode(array(
				"errno"=> $model->errno,
				"errmsg"=> $model->errmsg
			));
			return FALSE;
		}
		return TRUE;			
	}

	public function registerAction() {
		// 获取参数
		$uname = $this->getRequest()->getPost("uname", false);
		$pwd = $this->getRequest()->getPost("pwd", false);
		$email = $this->getRequest()->getPost('email', false);
		$mobile = $this->getRequest()->getPost('mobile', false);	
		
		$email = !$email ? null : $email;
		$mobile = !$mobile ? null : $mobile;
		
		if (!$uname || !$pwd) {
			echo json_encode(array(
				"errno"=>-1002,
				"errmsg"=>"用户名与密码必须传递"
			));
			return FALSE;
		} else if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)){
			echo json_encode(array(
				"errno"=>-1002,
				"errmsg"=>"邮件信息不符合标准"
			));
			return false;
		}

		// 调用数据模块，做登录验证
		$model = new UserModel();
		if ($model->register( trim($uname), trim($pwd), $email, $mobile ) ) {
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

