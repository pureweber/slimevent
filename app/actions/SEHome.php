<?php
/**
 * SEHomeClass
 *
 * @package   Slimevent
 **/

/**
 * Action for Home
 */

class SEHome extends SECommon{

	function test()
	{
		$data = array();
		$event = array();
		$event['title'] = "bbb";
		$event['region'] = "2";
		$event['category'] = "40";
		$event['begin_time'] = "119912";
		$data['name'] = "aaa";
		$data['no'] = "bb831";
		$data['sex'] = "ccfemale";
		$data['class'] = "dd667";
		$data['college'] = "ffhitaaa";
		$data['major'] = "eeaa";
		$data['avatar'] = "gg121";
		$data['email'] = "hhaaa@mail";
		$data['phone'] = "ii2222111";
		$r = JoinList::get_join_event(46);
		Code::dump($r);
	}


	function run()
	{
		$event = new SEEvent();
		$event->show_by("");
		echo Template::serve('index.html');
		//if(Account::is_login() === TRUE)
			//echo Template::serve('hello.html');
		//else
			//F3::reroute('/accounts/admin/login');
	}

	function my()
	{
		$gay = new SECommon();
		$gay->my();
	}

	//functio

	function show_login()
	{	
		switch(F3::get('GET.auth'))
		{
			case F3::get('CAS_AUTH'):
				$name = CAS::login();
				$pwd = F3::get('DEFAULT_PWD');
				if(Account::exists($name) === false)  //首次通过CAS登录
				{
					$group = F3::get('STUDENT_GROUP');
					$nickname = "S".$name;
					Admin::add_user($name, $pwd, $group, $nickname);
				}
				break;
			case F3::get('CLUB_AUTH'):
				echo Template::serve('club/login.html');
				return;
			default:
				F3::reroute('/');
		}

		Account::login($name, $pwd);
		F3::reroute('/');
	}

	function login()
	{
		$user_name = F3::get('POST.user_name');
		$user_pwd = F3::get('POST.user_pwd');

		$user = Account::login($user_name, $user_pwd); 

		if($user === false)
			F3::reroute('/club/login/?show_msg=1');  
		else 
			F3::reroute('/');
	}

	function logout()
	{
		switch(Account::the_user_group())
		{
			case F3::get('STUDENT_GROUP'):
				Account::logout();
	//			CAS::logout();
				break;
			default:
				Account::logout();
		}

		F3::reroute('/');
	}

};

?>
