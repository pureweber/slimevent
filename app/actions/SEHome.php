<?php

class SEHome{

	function run()
	{
		if(Account::is_login() === TRUE)
			echo Template::serve('hello.html');
		else
			F3::reroute('/accounts/login');
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
		echo Template::serve('accounts/login.html');
	}

	function login()  
	{
		$user_name = F3::get('POST.user_name');
		$user_pwd = F3::get('POST.user_pwd');

		$user = Account::valid($user_name, $user_pwd); 

		if($user === FALSE)
			//show_msg = 1 表示 密码不正确类型 其余可扩充
			F3::reroute('/accounts/login/?show_msg=1');  
		else
		{
			$user = Account::set_cookie($user);
			F3::reroute('/');
		}
	}

	function logout()
	{
		Account::unset_cookie();
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

		$now_user = Account::exists($user['stu_id']);
		if($now_user === FALSE)
		{
			$name = $user['stu_id'];
			$pwd = md5(trim($name));
			$group = F3::get('NORMAL_GROUP');

			$now_user = Account::insert($name, $pwd, $group);
			if($now_user === FALSE)
			{
				echo "插入数据库失败";
				return;
			}
		}
		else
			$now_user = Account::update($user);

		Account::login($now_user);
		F3::reroute('/');
		*/
	}

};

?>
