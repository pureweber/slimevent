<?php
class RENREN{

	static function login()
	{
		require_once 'app/lib/renren/config.inc.php';
		require_once 'app/lib/renren/RenrenOAuthApiService.class.php';
		require_once 'app/lib/renren/RenrenRestApiService.class.php';

		$url = "https://graph.renren.com/oauth/authorize?client_id=$config->APPID&response_type=code&scope=$config->scope&state=a%3d1%26b%3d2&redirect_uri=$config->redirecturi&x_renew=true";

		if(!isset($_GET["code"]))
			F3::reroute($url);

		$code = $_GET["code"];
		$oauthApi = new RenrenOAuthApiService;
		$post_params = array('client_id'=>$config->APIKey,
			'client_secret'=>$config->SecretKey,
			'redirect_uri'=>$config->redirecturi,
			'grant_type'=>'authorization_code',
			'code'=>$code
		);
		$token_url='http://graph.renren.com/oauth/token';
		$access_info=$oauthApi->rr_post_curl($token_url,$post_params);//使用code换取token
		$access_token=$access_info["access_token"];
		$expires_in=$access_info["expires_in"];
		$refresh_token=$access_info["refresh_token"];

		//获取用户信息RenrenRestApiService
		$restApi = new RenrenRestApiService;
		$params = array('fields'=>'uid,name,sex,birthday,mainurl,hometown_location,university_history,tinyurl,headurl','access_token'=>$access_token);
		$res = $restApi->rr_post_curl('users.getInfo', $params);//curl函数发送请求

		$user = Array();
		$user['uid'] = $res[0]['uid'];
		$user['name'] = $res[0]['name'];
		$user['group'] = F3::get('RENREN_GROUP_NAME');

		return $user;
	}
};
?>
