<?php
	/*
	** 短信数据模块
	*/
	class SmsModel {
		public $errno = 0;
		public $errmsg = "";

		private $_db = null;

		public function __construct() {
			$this->_db = new PDO("mysql:host=127.0.0.1;dbname=tjhzs_api;", "root", "seagull");
			// 设置SQL语句转换类型，保留数字类型
			$this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);		
		}
		
		public function send ($uid, $templateId) {
			$query = $this->_db->prepare("select `mobile` from `user` where `id`=?");
			$query->execute( array( intval($uid) ) );

			$ret = $query->fetchAll();

			if (!$ret || count($ret) != 1) {
				$this->errno = -4003;
				$this->errmsg = '手机信息查找失败';
				return false;
			}

			$userMobile = $ret[0]['mobile'];

			if (!$userMobile || !is_numeric($userMobile) || strlen($userMobile) !=11) {
				$this->errno = -4004;
				$this->errmsg = '用户手机信息不准确，记录的手机号为：'.(!$userMobile ? 'null' : $userMobile);
				return false;
			}
			
			// 第三方短信API账号
			$smsUn = "phpapitest";
			$smsPwd = "seagull";
			$sms = new ThirdParty_Sms($smsUn, $smsPwd);
			
			// 短信模板ID
			$template = $templateId;

			// 短信模板内参数设置
			$contentParam = array('code' => $sms->randNumber());

			$result = $sms->send($userMobile, $contentParam, $template);
			
			if ($result['stat'] == '100') {
				
				/* 短信发送成功后记录到数据库 */
				$query = $this->_db->prepare("insert into `sms_record` (`uid`, `contents`, `template`) VALUES (?, ?, ?, ?)");
				$ret = $query->execute( array($uid, json_encode($contentParam), $template) );
				
				if (!$ret) {
					/* 短信记录失败 */
					/* 短信记录失败怎么办？*/
					$this->errno = -4006;
					$this->errmsg = "消息发送成功，但发送记录失败。";
					return true;
				}

				$this->errno = 0;
				$this->errmsg = '';
				return true;
			} else {
				
				$this->errno = -4005;
				$this->errmsg = '发送失败：'.$result['stat'].'('.$result['message'].')';
				return false;
			}


		}
			
	}


?>
