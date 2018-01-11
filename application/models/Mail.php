<?php

	require __DIR__.'/../../vendor/autoload.php';
	use Nette\Mail\Message;


	class MailModel {
		public $errno = 0;
		public $errmsg = "";

		private $_db = null;

		public function __construct() {
			$this->_db = new PDO("mysql:host=127.0.0.1;dbname=tjhzs_api;", "root", "seagull");
			// 设置SQL语句转换类型，保留数字类型
			$this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);		
		}
		
		public function send ($uid, $title, $contents) {
			$query = $this->_db->prepare("select `email` from `user` where `id`=?");
			$query->execute( array( intval($uid) ) );

			$ret = $query->fetchAll();

			if (!$ret || count($ret) != 1) {
				$this->errno = -3003;
				$this->errmsg = '用户信息不正确';
				return false;
			}

			$userMail = $ret[0]['email'];

			if (!filter_var($userMail, FILTER_VALIDATE_EMAIL) ) {
				$this->errno = -3004;
				$this->errmsg = '用户邮箱信息不符合标准，邮箱地址为：'.$userMail;
				return false;
			}

			$mail = new Message;
			$mail->setFrom('PHP test <php_api_test@126.com>')
				->addTo($userMail)
				->setSubject($title)
				->setBody($contents);

			$mailer = new Nette\Mail\SmtpMailer([
				'host' => 'smtp.126.com',
				'username' => 'php_api_test@126.com',
				'password' => 'june820',
				'secure' => 'ssl'
			]);

			$rep = $mailer->send($mail);

			return true;
		}
			
	}


?>
