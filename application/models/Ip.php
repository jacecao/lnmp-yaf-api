<?php
	/*
	** IP数据查询模块
	*/
	class IpModel {
		
		public $errno = 0;
		public $errmsg = "";

		private $_db = null;

		public function __construct() {
			//$this->_db = new PDO("mysql:host=127.0.0.1;dbname=tjhzs_api;", "root", "seagull");
			// 设置SQL语句转换类型，保留数字类型
			//$this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);		
		}
		
		public function get ($ip) {
			
			$rep = ThirdParty_Ip::find($ip);
			
			//print_r($rep);
			
			return $rep;

		}
			
	}


?>
