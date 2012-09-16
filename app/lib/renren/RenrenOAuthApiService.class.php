<?php
/*
 * 调用人人网oauth API的客户端类，本类需要继承HttpRequestService类方可使用
 * 要求最低的PHP版本是5.2.0，并且还要支持以下库：cURL, Libxml 2.6.0
 * This class for invoke RenRen RESTful Webservice
 * It MUST be extends RESTClient
 * The requirement of PHP version is 5.2.0 or above, and support as below:
 * cURL, Libxml 2.6.0
 *
 * @Author mike on 17:54 2011/12/21.
 */

require_once 'HttpRequestService.class.php';

 class RenrenOAuthApiService extends HttpRequestService{

	private $_config;
	private $_params		=	array();

	
	public function __construct(){
		global $config;
		
		parent::__construct();
		
//		$this->_config = $config;
$this->_config				= new stdClass;

$this->_config->APIURL		= 'http://api.renren.com/restserver.do'; //RenRen网的API调用地址，不需要修改
$this->_config->APPID		= '211462';	//你的API Key，请自行申请
$this->_config->APIKey		= 'c750d9624d08427ca4f4b0dbde793e87';	//你的API Key，请自行申请
$this->_config->SecretKey	= '2d6503d299014da5981e21b873b82b55';	//你的API 密钥
$this->_config->APIVersion	= '1.0';	//当前API的版本号，不需要修改
$this->_config->decodeFormat	= 'json';	//默认的返回格式，根据实际情况修改，支持：json,xml

//$this->_config->redirecturi= 'http://127.0.0.1/slimevent/renren.php';//你的获取code的回调地址，也是accesstoken的回调地址
$this->_config->redirecturi= 'http://127.0.0.1/slimevent/login/renren';//你的获取code的回调地址，也是accesstoken的回调地址
$this->_config->scope='publish_feed,photo_upload';	

		if(empty($this->_config->APIURL) || empty($this->_config->APIKey) || empty($this->_config->SecretKey)){
			throw new exception('Invalid API URL or API key or Secret key, please check config.inc.php');
			}
	}

     /**
      * GET wrapper
      * @param method String
      * @param parameters Array
      * @return mixed
      */
	public function GET(){

		$args = func_get_args();
		$this->paramsMerge($args[1])
			 ->generateSignature();
		$reqUrl=$args[0];
		#Invoke
		unset($args);

		return $this->_GET($reqUrl, $this->_params);
	
	}

     /**
      * POST wrapper，基于curl函数，需要支持curl函数才行
      * @param method String
      * @param parameters Array
      * @return mixed
      */
	public function rr_post_curl(){

		$args = func_get_args();
		$this->paramsMerge($args[1])
			 ->generateSignature();
		$reqUrl=$args[0];
		#Invoke
		unset($args);

		return $this->_POST($reqUrl, $this->_params);
	
	}
     /**
      * Generate signature for sig parameter
      * @param method String
      * @param parameters Array
      * @return RenRenClient
      */
	private function generateSignature(){
			$arr = $this->_params;
			foreach($arr AS $k=>$v){
				$v=$this->convertEncoding($v,$this->_encode,"utf-8");
				$arr[$k]=$v;//转码，你懂得
			}
			
			$this->_params = $arr;

			unset($str, $arr);

			return $this;
	}
	private function paramsMerge($params){
		$this->_params = $params;
		return $this;
	}
	
	public function rr_post_fopen(){

		$args = func_get_args();
		$this->paramsMerge($args[1])
			 ->generateSignature();
		$reqUrl=$args[0];

		#Invoke
		unset($args);

		return $this->_POST_FOPEN($reqUrl, $this->_params);
	
	}
	
	
	
 }
?>
