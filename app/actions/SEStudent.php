<?php
/**
 * Student
 *
 * @package   Slimevent
 **/

class SEStudent{

	function __construct(){
		SECommon::set_unread_msg_num();
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
	}

}
