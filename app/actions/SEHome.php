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

	function run()
	{
		echo Template::serve('index.html');
		//if(Account::is_login() === TRUE)
			//echo Template::serve('hello.html');
		//else
			//F3::reroute('/accounts/admin/login');
	}

	function show_login()
	{	
		switch(F3::get('GET.auth'))
		{
			case 'cas':
				$user = CAS::login();

				$name = $user['stu_id'];
				$pwd = md5(trim($name));
				$group = F3::get('STUDENT_GROUP');

				if(Student::exists($name) === FALSE)
					Student::insert($name, $pwd, $group);

				Account::login($name, $pwd);
				break;
			case 'club':
				echo Template::serve('club/login.html');
				return;
			default:
				F3::reroute('/');
		}

		F3::reroute('/');

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

	function logout()
	{
		switch(Account::the_user_group())
		{
			case F3::get('STUDENT_GROUP'):
				Account::logout();
				CAS::logout();
				break;
			default:
				Account::logout();
		}

		F3::reroute('/');
	}

};

?>
