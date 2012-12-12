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
	}


	function feedback(){
	}

	function show_feedback(){
		echo "feedback";
	}

	function find(){
		$query = F3::get("GET");
		Code::dump($query);
	}

	function show_find(){
		$category = Category::get_all();
		F3::set("category", $category);
		echo Template::serve('find/find.html');
	}


	function run()
	{
		$event = new SEEvent();
		$event->show_by("");
		
		echo Template::serve('index.html');
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

	function get_praise_info()
	{
		$uid = trim(F3::get("POST.uid"));
		$eid = trim(F3::get("POST.eid"));
		$num = count(PraiseList::get_praise_user($eid));

		if($uid == "")  //当前属于没有登录
			echo "推一下($num)";
		else if($uid == Account::the_user_id())  //合法登录用户
		{
			if(PraiseList::is_user_praise_event($uid, $eid) === true)
				echo "已推($num)";
			else
				echo "推一下($num)";
		}
		else
			echo "非法登录";
	}

	function get_join_info()
	{
		$uid = trim(F3::get("POST.uid"));
		$eid = trim(F3::get("POST.eid"));
		$num = count(JoinList::get_join_user($eid));

		if($uid == "")  //当前属于没有登录
			echo "我要报名($num)";
		else if($uid == Account::the_user_id())  //合法登录用户
		{
			if(JoinList::is_user_join_event($uid, $eid) === true)
				echo "取消报名($num)";
			else
				echo "我要报名($num)";
		}
		else
			echo "非法登录";
	}
};

?>
