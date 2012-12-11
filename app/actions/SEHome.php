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
		//$data['introduction'] = "luggwoaipin";
		//Org::edit_basic_info($data);
//		Student::register(111);
		//echo Event::create($event);
		//var_dump(Event::show(20));
		//var_dump(Event::update(200,$event));
		//echo EDB::counts('event','eid',7);
		//Student::add_basic_info($data);
		//Student::edit_basic_info($data);
		//Admin::edit_user_info(26,$data);
		//Event::show(1);
		//$r = Category::get_all();
		//Code::dump($r);
		//Admin::edit_user_info(1,'student',$data);
	//	Admin::reset_user_pwd(11,2222);
		//Admin::add_user_to_black_list(10);
		//Admin::remove_user_from_black_list(10);
		//Admin::change_user_status(11,"normal");
		//echo Admin::get_user_group(10);
		//$r = Account::login("orghit","123");
		//var_dump($r);
		//echo Account::the_user_id();
		//var_dump(Account::exists("kjlmfeaa"));
		//echo Account::the_user_group();
		//echo Account::the_user_name();
		//Account::edit_event(6,$event);
		//Service::event_audit_fail(5);
		//Service::event_audit_pass(50);
		//$r = Service::get_event_to_audit();
		//var_dump($r);
//		Service::event_audit_pass(2);
		//Service::event_audit_fail(2);
		//echo Admin::add_user("zhukiieaaaiii", "123","admin","normal");
		//Admin::reset_user_pwd(20,"123");

		//Admin::get_user(18);
		//Student::join_event(45);
		//Student::unjoin_event(45);
		//Student::praise_event(35);
//		Student::unpraise_event(34);
		//Student::unjoin_event(34);
		//$r = JoinList::get_join_user(34);
		//$r = PraiseList::get_praise_user(2);
		//$r = PraiseList::get_praise_event(46);
		$r = JoinList::get_join_event(46);
		Code::dump($r);
	}


	function run()
	{
		SECommon::show_by("");
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
