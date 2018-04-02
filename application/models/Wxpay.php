<?php
	/*
	** 支付模块
	*/

	/*
	** 引入微信支付接口，这里采用普通扫码第二种模式
	*/
		
	$wx_pay_lib_path = dirname(__FILE__).'/../library/ThirdParty/Wxpay/';
	
	include_once($wx_pay_lib_path.'WxPay.Api.php');
	include_once($wx_pay_lib_path.'WxPay.Notify.php');
	include_once($wx_pay_lib_path.'WxPay.NativePay.php');
	include_once($wx_pay_lib_path.'WxPay.Data.php');
	
	
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
		
		// 请求支付，返回支付地址
		public function qrcode ($billId) {
			$query = $this->_db->prepare('select * from `bill` where `id`= ?');
			$query->execute(array($billId));
			$ret = $query->fetchAll();

			if (!$ret || count(ret) != 1) {
				$this->errno = -6009;
				$this->errmsg = '找不到账单信息';
				return false;
			}
			$bill = $ret[0];

			$query = $this->_db->prepare('select * from `item` where `id`= ?');
			$query->execute(array($bill['itemid']));
			$ret = $query->fetchAll();
			if (!$ret || count($ret) != 1) {
				$this->errno = -6010;
				$this->errmsg = '找不到商品信息';
				return false;
			}
			$item = $ret[0];
			
			$input = new WxPayUnifiedOrder();
			$input->SetBody($item['name']);
			$input->SetAttach($billId);
			$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
			$input->SetTotal_fee($bill['price']);
		    $input->SetTime_start(date("YmdHis"));
			$input->SetTime_expire(date("YmdHis", time() + 600));
			$input->SetGoods_tag($item['name']);
		    $input->SetNotify_url("https://api.mch.weixin.qq.com/pay/unifiedorder");
			$input->SetTrade_type("NATIVE");
		    $input->SetProduct_id($billId);

			$notify = new NativePay();
			$result = $notify->GetPayUrl($input);
			$url = $result["code_url"];		
			return $url;
			
		}






	}
?>
