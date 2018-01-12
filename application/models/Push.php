<?php
	/*
	** 推送数据模块
	*/

/*
** 引入个推的cdk
*/
$getui_path = dirname(__FILE__).'/../library/ThirdParty/Getui/';
require_once($getui_path.'IGt.Push.php');
require_once($getui_path.'igetui/IGt.AppMessage.php');
require_once($getui_path.'igetui/IGt.APNPayload.php');
require_once($getui_path.'igetui/template/IGt.BaseTemplate.php');
require_once($getui_path.'IGt.Batch.php');
require_once($getui_path.'igetui/utils/AppConditions.php');

// http的域名
define('HOST', 'http://sdk.open.api.igexin.com/apiex.htm');
// APP信息初始设置
define('APPKEY', 'LOkIuY75p4AeheeKU3lm29');
define('APPID', '01bmpXGakeAn9tAU6IodN');
define('MASTERSECRET', 'i3Py4MvvPI6CIddiJtVVN8');
define('CID','');
define('DEVICETOKEN','');
define('Alias','请输入别名');


	class PushModel {
		public $errno = 0;
		public $errmsg = "";

		private $_db = null;

		public function __construct() {
			//$this->_db = new PDO("mysql:host=127.0.0.1;dbname=tjhzs_api;", "root", "seagull");
			// 设置SQL语句转换类型，保留数字类型
			//$this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);		
		}
	
		public function single($cid, $msg="test contents") {	
			
			$igt = new IGeTui(HOST, APPKEY, MASTERSECRET);

			$template = $this->_IGtTransmissionTemplate($msg);
			
			//个推信息体
			$message = new IGtSingleMessage();
			
			$message->set_isOffline(true);//是否离线
			$message->set_offlineExpireTime(3600*12*1000);//离线时间
			$message->set_data($template);//设置推送消息类型
			$message->set_PushNetWorkType(1);//设置是否根据WIFI推送消息，1为wifi推送， 0为不限制推送
			//接收方
			$target = new IGtTarget();
			$target->set_appId(APPID);
			$target->set_clientId($cid);
		 // $target->set_alias(Alias);
			
			
			try {
				
				$rep = $igt->pushMessageToSingle($message, $target);
			} catch (RequestException $e){
				
				$requstId =$e->getRequestId();
				$rep = $igt->pushMessageToSingle($message, $target,$requstId);
				$this->errno = -7003;
				$this->errmsg = $rep['result'];
				return false;
			}
			
			return true;

		}

		public function toAll($msg) {
		
			$igt = new IGeTui(HOST,APPKEY,MASTERSECRET);
		    $template = $this->_IGtTransmissionTemplate($msg);
		    //$template = IGtLinkTemplateDemo();
		    //个推信息体
		    //基于应用消息体
		    $message = new IGtAppMessage();
		    $message->set_isOffline(true);
		    $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
			$message->set_data($template);
			
			$appIdList=array(APPID);
			$phoneTypeList=array('ANDROID');
			//$provinceList=array('浙江');
			//$tagList=array('haha');
			//用户属性
			//$age = array("0000", "0010");
				
			$cdt = new AppConditions();
			$cdt->addCondition(AppConditions::PHONE_TYPE, $phoneTypeList);
			//$cdt->addCondition(AppConditions::REGION, $provinceList);
			//$cdt->addCondition(AppConditions::TAG, $tagList);
			//$cdt->addCondition("age", $age);

			$message->set_appIdList($appIdList);
			//$message->set_conditions($cdt->getCondition());

			
			try {
						
				$rep = $igt->pushMessageToApp($message);
			} catch (RequestException $e){
				
				$requstId =$e->getRequestId();
				$rep = $igt->pushMessageToApp($message);
				$this->errno = -7003;
				$this->errmsg = $rep['result'];
				return false;
			}
			
			return true;	
		}

		private function _IGtTransmissionTemplate($msg) {
			$template =  new IGtTransmissionTemplate();
			$template->set_appId(APPID);//应用appid
			$template->set_appkey(APPKEY);//应用appkey
			$template->set_transmissionType(1);//透传消息类型
			$template->set_transmissionContent($msg);//透传内容
			
			//APN高级推送
			$apn = new IGtAPNPayload();
			$alertmsg=new DictionaryAlertMsg();
			$alertmsg->body="body";
			$alertmsg->actionLocKey="ActionLockey";
			$alertmsg->locKey="LocKey";
			$alertmsg->locArgs=array("locargs");
			$alertmsg->launchImage="launchimage";
			// IOS8.2 支持
			$alertmsg->title="Title";
			$alertmsg->titleLocKey="TitleLocKey";
			$alertmsg->titleLocArgs=array("TitleLocArg");
			
			$apn->alertMsg=$alertmsg;
			$apn->badge=7;
			$apn->sound="";
			$apn->add_customMsg("payload","payload");
			$apn->contentAvailable=1;
			$apn->category="ACTIONABLE";
			$template->set_apnInfo($apn);		
			
			return $template;	
		}
	
	}


?>
