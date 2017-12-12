<?php
	class ArtController extends Yaf_Controller_Abstract {
			
		public function indexAction() {
			return $this->listAction();
		}
	
		// add function start
		public function addAction($artId = 0) {
			if ($this->_checkAdmin()) {
				
				// 获取参数
				$title = $this->getRequest()->getPost("title", false);
				$contents= $this->getRequest()->getPost("contents", false);
				$author = $this->getRequest()->getPost("author", false);
				$cate = $this->getRequest()->getPost("cate", false);

				if (!$title || !$contents || !$author || !$cate) {
					echo json_encode(array(
						"errno"=>-2002,
						"errmsg"=>"here somy data was null"
					));
					return FALSE;
				}
				// 调用model
				$model = new ArtModel();
				$lastId = $model->add(trim($title), trim($contents), trim($author), trim($cate), $artId);
				if ($lastId) {
					echo json_encode(array(
						"errno"=>0,
						"errmsg"=>"",
						"data"=>array("lastId"=>$lastId)
					));
				} else {
					echo json_encode(array(
						"errno"=>$model->errno,
						"errmsg"=>$model->errmsg
					));		
				}	
				return TRUE;
			}
		}
		// add function end

		// edit function start
		public function editAction() {
			
			if ($this->_checkAdmin()) {
				$artId = $this->getRequest()->getQuery("artId", "0");
				if (is_numeric($artId) && $artId) {
					return $this->addAction($artId);
				} else {
					echo json_encode(array(
						"errno"=>-2003,
						"errmsg"=> "miss art id"
					));
				}	
			}
		}
		// edit function end
		
		// del function start
		public function  delAction() {
			if ($this->_checkAdmin()) {
				
				$artId = $this->getRequest()->getQuery("artId", "0");
				if (is_numeric($artId) && $artId) {
					
					$model = new ArtModel();
					if ($model->del($artId)) {
						echo json_encode(array(
							"errno"=>0,
							"errmsg"=>"",
						));
					} else {
						echo json_encode(array(
							"errno"=>$model->errno,
							"errmsg"=>$model->errmsg
						));
					}

				} else {
					
					echo json_encode(array(
						"errno"=>-2003,
						"errmsg"=>"缺少文章ID参数"
					));

				}

			}
		}
		// del function end	
		
		// status function start
		public function statusAction() {
			if ($this->_checkAdmin()) {
				$artId = $this->getRequest()->getQuery("artId", "0");
				$status = $this->getRequest()->getQuery("status", "offline");
				
				if (is_numeric($artId) && $artId) {
					$model = new ArtModel();
					
					if ($model->status($artId, $status)) {
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
						"errno"=>-2003,
						"errmsg"=>"缺少文章id参数"
					));
				}

			}
		}
		// status function end
		
		// get function start
		public function getAction() {
			$artId = $this->getRequest()->getQuery("artId", "0");
			if (is_numeric($artId) && $artId) {
				
				$model = new ArtModel();
				$data = $model->get($artId);
				if ($data) {
					
					echo json_encode(array(
						"errno"=>0,
						"errmsg"=>"",
						"data"=>$data
					));
				} else {
				
					echo json_encode(array(
						"errno"=>-2009,
						"errmsg"=>"获取文章信息失败"
					));
				}
				
			} else {
					
				echo json_encode(array(
					"errno"=>-2003,
					"errmsg"=>"缺少文章ID"
				));
			}
			return true;
		}
		// get function end
		
			
		// _isAdmin start
		private function  _isAdmin() {
			return TRUE;
		}
		// _isAdmin end
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
				return true;
			}
		}
		// _checkadmin end
			
	}


?>
