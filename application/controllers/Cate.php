<?php
 /*
 ** 分类信息
 */
class CateController extends Yaf_Controller_Abstract {

	public function indexAction() {
        return $this->listAction();
	}
	
	// add function start
	// 增加分类
	public function addAction($cateId = 0) {
		if ($this->_checkAdmin()) {
			// 获取参数
			$name = $this->getRequest()->getPost("name", false);
			if (!$name) {
				echo json_encode(array(
					"errno"=>-3002,
					"errmsg"=>"the cate name was null"
				));
				return FALSE;
			}

			// 调用model
			$model = new CateModel();
			$lastId = $model->add(trim($name), $cateId);
		
			if ($lastId) {
				echo json_encode(array(
					"errno"=>0,
					"errmsg"=>"",
					"data"=> array("lastId"=>$lastId)
				));		
			} else {
				echo json_encode(array(
					"errno" => $model->errno,
					"errmsg" => $model->errmsg
				));
			}

		}

		return FALSE;
	}
	// add function end
	
	// edit function start
	public function editAction() {
		if ($this->_checkAdmin()) {
			$cateId = $this->getRequest()->getQuery("cateId", "0");
		
			if (is_numeric($cateId) && $cateId) {
				return $this->addAction($cateId);
			} else {
				echo json_encode(array(
					"errno"=> -3003,
					"errmsg"=> "cate id is null"
				));
				return FALSE;
			}

		}
	}
	// edit function end
	
	// del function start
	public function delAction() {
		if ($this->_checkAdmin()) {
			
			$cateId = $this->getRequest()->getQuery("cateId", "0");
			
			if (is_numeric($cateId) && $cateId) {
					
				$model = new CateModel();
				if ($model->del($cateId)) {
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
				
			} else {
				echo json_encode(array(
					"errno"=>-3003,
					"errmsg"=>"缺少分类ID参数"
				));
			}
			
		}

		return FALSE;
	}
	// del function end
	
	// list function start
	public function listAction() {
		$model = new CateModel();
		$data = $model->list();
		if (!$data) {
			
			echo json_encode(array(
				"errno"=>$model->errno,
				"errmsg"=>$model->errmsg
			));
			return false;
		} else {
			
			echo json_encode(array(
				"errno"=>0,
				"errmsg"=>"",
				"data"=>$data
			));
		}
		return FALSE;
	}

	// list function end

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
