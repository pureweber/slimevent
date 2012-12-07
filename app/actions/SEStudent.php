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

		$name = $user['stu_id'];
		$pwd = md5(trim($name));
		$group = F3::get('STUDENT_GROUP');

		if(Student::exists($name) === FALSE)
			Student::insert($name, $pwd, $group);

		Account::login($name, $pwd);

		F3::reroute('/');
	}

}
