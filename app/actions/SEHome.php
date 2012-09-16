<?php

class SEHome{

	function run(){
		if(Account::is_login() === TRUE)
			echo Template::serve('hello.html');
		else
			echo Template::serve('login.html');
	}

	function logout()
	{
		Account::logout();
		F3::reroute('/');
	}

	function login()
	{
		switch(F3::get('GET.way'))
		{
			case 'cas':
				$user = CAS::login();
				break;
			case 'renren':
				$user = RENREN::login();
				break;
			default:
				F3::reroute('/');
		}

		$now_user = Account::exists($user);
		if($now_user === FALSE)
		{
			$now_user = Account::insert($user);
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
	}

};

?>
