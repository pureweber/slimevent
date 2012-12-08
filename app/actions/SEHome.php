<?php
/**
 * SEHomeClass
 *
 * @package   Slimevent
 **/

/**
 * Action for Home
 */

class SEHome{

	function __construct(){
		SECommon::set_unread_msg_num();
	}

	function run()
	{
		echo Template::serve('index.html');
		//if(Account::is_login() === TRUE)
			//echo Template::serve('hello.html');
		//else
			//F3::reroute('/accounts/admin/login');
	}

	function logout()
	{
		Account::logout();
		F3::reroute('/');
	}

	function auth_login()
	{
		switch(F3::get('GET.auth'))
		{
			case 'cas':
				$user = CAS::login();
				break;
			default:
				F3::reroute('/');
		}

		$name = $user['stu_id'];
		$pwd = md5(trim($name));
		$group = F3::get('STUDENT_GROUP');

		if(Student::exists($name) === FALSE)
			Student::insert($name, $pwd, $group);

		Account::login($name, $pwd);

		F3::reroute('/');
	}

	function show_club_login()
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

	function club_login()
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
};

?>
