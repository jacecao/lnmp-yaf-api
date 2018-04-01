<?php
	/*
	** 支付模块
	*/
	class WxpayModel {
		public $errno = 0;
		public $errmsg = "";

		private $_db = null;

		public function __construct() {
			$this->_db = new PDO("mysql:host=127.0.0.1;dbname=tjhzs_api;", "root", "seagull");
			// 设置SQL语句转换类型，保留数字类型
			$this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);		
		}
		
		public function createbill ($itemid, $uid) {
			$query = $this->_db->prepare("select * from `item` where `id`=?");
			$query->execute( array( intval($itemid) ) );

			$ret = $query->fetchAll();
			if (!$ret || count($ret) != 1) {
				$this->errno = -6003;
				$this->errmsg = '找不到该商品';
				return false;
			}
			
			$item = $ret[0];
			
			// 检查商品是否过期
			if (strtotime($item['etime']) <= time()) {
				$this->errno = -6005;
				$this->errmsg = '商品过期，无法购买';
				return false;
			}
			
			// 检查商品是否有库存
			if (intval($item['stock']) <= 0 ) {
				$this->errno = -6005;
				$this->errmsg = '商品库存不足';
				return false;
			}
		
			// 创建账单
			$query = $this->_db->prepare("insert into `bill` (`itemid`, `uid`, `price`, `status`) VALUES (?, ?, ?, 'unpaid')");
			$ret = $query->execute(array($itemid, $uid, intval($item['price'])));
			if (!$ret) {
				$this->errno = -6006;
				$this->errmsg = '创建账单失效';
				return false;		
			}

			$last_bill_id = intval($this->_db->lastInsertId());

			// 订单创建成功后，库存-1
			$query = $this->_db->prepare("update `item` set `stock` = `stock` - 1 where `id` = ?");	
			$ret = $query->execute(array($itemid));
			if (!$ret) {
				$this->errno = -6007;
				$this->errmsg = '更新库存失败';
				return false;
			}
			// 返回最新订单id
			return $last_bill_id;
		}
			
	}


?>
