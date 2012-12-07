<?php
/**
 * Club
 *
 * @package   Slimevent
 **/

class SEClub{

	function __construct(){
		SECommon::set_unread_msg_num();
	}

	function show_login()
	{
		//show_msg = 1 表示 密码不正确类型 其余可扩充
		if(F3::get('GET.show_msg') == 1)
		{
			$msg = "用户名或密码错误";
			F3::set("msg", $msg);
			F3::set("show_msg", "true");
		}
		echo Template::serve('club/login.html');
	}

	function login()
	{
		$user_name = F3::get('POST.user_name');
		$user_pwd = F3::get('POST.user_pwd');

		$user = Account::login($user_name, $user_pwd); 

		if($user === FALSE){
			F3::reroute('/club/login/?show_msg=1');  
		} else {
			F3::reroute('/');
		}
	}

}


