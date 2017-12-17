<?php
	class CateModel {
		public $errno = 0;
		public $errmsg = "";

		private $_db = null;

		public function __construct() {
			$this->_db = new PDO("mysql:host=127.0.0.1;dbname=tjhzs_api;", "root", "seagull");
			// 设置SQL语句转换类型，保留数字类型
			$this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);		
		}
		
		// add function start
		public function add($name, $cateId = 0) {
			$isEdit = false;
	
			if ($cateId != 0 && is_numeric($cateId)) {
				// edit
				$query = $this->_db->prepare("select count(*) from `cate` where `id` = ?");
				$query->execute(array($cateId));
				// 查找数据库中有没有指定ID的分类
				$ret = $query->fetchAll();
				if (!$ret || count($ret) != 1) {
					$this->errno = -3004;
					$this->errmsg = "找不到该分类";
					return false;
				}
				$isEdit = true;
			}

			// 插入或更新分类
			$data = array($name);
			// 编辑分类
			if ($isEdit) {
				$query = $this->_db->prepare("update `cate` set `name`=? where `id`=?");
				$data[] = $cateId;
			} else {
				// 新增分类
				$query = $this->_db->prepare("insert into `cate` (`name`) VALUES (?)");
			}

			$ret = $query->execute($data);
			if (!$ret) {
				$this->errno = -3006;
				$this->errmsg = "操作失败，ErrInfo:".end($query->errorInfo());
				return false;
			}

			// 返回分类的ID
			if ($isEdit) {
				return intval($cateId);
			} else {
				return intval($this->_db->lastInsertId());
			}
		}
		// add function end


		// del function start
		public function del($cateId) {
			$query = $this->_db->prepare("delete from `cate` where `id`=?");
			$ret = $query->execute( array( intval($cateId) ) );

			if (!$ret) {
				$this->errno = -3002;
				$this->errmsg = "删除失败，ErrInfo".end($query->errorInfo());
				return false;
			}
			
			return true;
		}

		// del function end

		// list function start
		public function list() {
			$query = $this->_db->prepare("select `id`, `name` from `cate` order by `id`");
			$stat = $query->execute();
			$ret = $query->fetchAll();

			if (!$ret) {
				$this->errno = -3001;
				$this->errmsg = "获取分类列表失败，ErrInfo".end($query->errorInfo());
				return false;
			}

			$data = array();

			foreach ($ret as $item) {
				$data[] = array(
					"id" => $item['id'],
					"name" => $item['name']
				);
			}

			return $data;
		}
		// list function end

			
	}


?>
