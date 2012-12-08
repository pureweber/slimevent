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

	function test()
	{
		//Admin::add_user("free", "123", "student", "normal");
		//Admin::add_user("root", "123", "admin", "normal");
		//Admin::add_user("service", "123", "service", "normal");
		//Admin::add_user("cslub", "123", "club", "normal");
		//Admin::add_user("orghit", "123", "org", "normal");
		$data = array();

		$data['name'] = "aaa";
		$data['no'] = "bb831";
		$data['sex'] = "ccfemale";
		$data['class'] = "dd667";
		$data['college'] = "ffhitaaa";
		$data['major'] = "eeaa";
		$data['avatar'] = "gg121";
		$data['email'] = "hhaaa@mail";
		$data['phone'] = "ii2222111";
		$data['introduction'] = "luggwoaipin";
		//Org::edit_basic_info($data);
		Student::register(111);

		//Student::add_basic_info($data);
		//Student::edit_basic_info($data);
		//Admin::edit_user_info(14,$data);
		//Admin::edit_user_info(1,'student',$data);
	//	Admin::reset_user_pwd(11,2222);
		//Admin::add_user_to_black_list(10);
		//Admin::remove_user_from_black_list(10);
		//Admin::change_user_status(11,"normal");
		//echo Admin::get_user_group(10);
		//$r = Account::login("orghit","123");
		//var_dump($r);
		//echo Account::the_user_id();
		//echo Account::the_user_group();
		//echo Account::the_user_name();
	}


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
			case F3::get('CAS_AUTH'):
				$user = CAS::login();

				$name = $user['stu_id'];
				$pwd = md5(F3::get('DEFAULT_PWD'));
				$group = F3::get('STUDENT_GROUP');

				if(Student::exists($name) === FALSE)
					Student::insert($name, $pwd, $group);

				Account::login($name, $pwd);
				break;
			case F3::get('CLUB_AUTH'):
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
