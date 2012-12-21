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

	function __construct()
	{
		parent::__construct();
	}

	function test()
	{
		$file = Sys::resize_image("./temp/4.jpg",170,220);
		$file2 = Sys::cutphoto("./temp/4.jpg","./temp/101.jpg",170,220);
		imagejpeg($file, "./temp/100.jpg");
	}

	function feedback(){
		$content = F3::get("POST.content");
		Feedback::add($content);
	}

	function show_feedback(){
		F3::set("route", array("feedback"));
		echo Template::serve('feedback/feedback.html');
	}

	function filter($r){
		$con = "";
		$data = array();
		//$a = array("category", "region");
		// 分类
		$value = F3::get("GET.category");
		if($value != null && $value != "all"){
			$con .= " `event`.`category_id` = :category AND ";
			$data[':category'] = $value;
		}

		// 校区
		$value = F3::get("GET.region");
		if($value != null && $value != "all"){
			$con .= " `event`.`region` = :region AND ";
			$data[':region'] = $value;
		}

		// 时间范围
		$value = F3::get("GET.range");
		if($value != null && $value != "all"){
			$now = time();
			$a_day = 60 * 60 * 24;
			$a_week = $a_day * 7;
			$today_date = floor($now / $a_day);
			$tonight= ($today_date + 1) * $a_day;
			switch($value){
				case 'today':
					$con .= "`begin_time` < $tonight AND `begin_time` > $now AND ";
					break;
				case 'tomorrow':
					$tomorrow = ($today_date + 2) * $a_day;
					$con .= "`begin_time` < $tomorrow AND `begin_time` > $tonight AND ";
					break;
				case 'week':
					$week = ($today_date + 8) * $a_day;
					$con .= "`begin_time` < $week AND `begin_time` > $now AND ";
					break;
				case 'weedend':
					break;
				default:
					break;
			}
		}

		//时间状态
		$value = F3::get("GET.time");
		//Code::dump($value);
		if($value != null && $value != "all"){
			$now = time();
			if($value == F3::get('EVENT_NOT_BEGIN'))  //尚未开始
				$con .= "`begin_time` > $now AND ";
			else if($value == F3::get('EVENT_IS_RUNNING'))   //进行中
				$con .= "`begin_time` < $now AND `end_time` > $now AND ";
			else  //已结束
				$con .= "`end_time` < $now AND ";
		}

		$r['con'] = $con.$r['con'];
		$r['array'] = array_merge($data, $r['array']);

		return $r;
	}

	function find_by($key, $word, $order){
		$data = array(':a' => $word);
		$note = "";
		switch($key)
		{
			case 'keyword':
				if($word == '-'){
					$note .= "列出全部活动";
					$con = " `event`.`status` = :s ";
					$data = array(':s'=>F3::get("EVENT_PASSED_STATUS"));
				}else{
					$note .= "根据关键词:<span class='label label-inverse'>{$word}</label>";
					$con = "( title LIKE :a OR label LIKE :b OR introduction LIKE :c )
						AND `event`.`status` = :s GROUP BY(`event`.`eid`) ";
					$word = '%'.$word.'%';
					$data = array(':a'=>$word, ':b'=>$word, ':c'=>$word, ':s'=>F3::get("EVENT_PASSED_STATUS"));
				}
				break;
			case 'label':
				$note .= "根据标签:<span class='label label-inverse'>{$word}</label>";
				$con = " label LIKE :b AND `event`.`status` = :s GROUP BY(`event`.`eid`) ";
				$word = '%'.$word.'%';
				$data = array(':b'=>$word, ':s'=>F3::get("EVENT_PASSED_STATUS"));
				break;
			case 'category_id':
			case 'category':
				$con = "category_id = :a ";
				$c = Category::get_name($word);
				//$note .= "类别:<span class='label label-inverse'>{$c}</span>";
				break;
			case 'organizer_id':
			case 'organizer':
				$con = "organizer_id = :a ";
				$info = Account::get_user($word);
				$note .= "根据主办(发起):<span class='label label-inverse'>{$info['nickname']}</label>";
				break;
			case 'region':
				$con = "region = :a ";
				$region = F3::get("REGION");
				//$note .= "校区:<strong>{$region[$word]}</strong>";
				break;
			case 'time_status':
			case 'time':
				$now = time();
				if($word == F3::get('EVENT_NOT_BEGIN'))  //尚未开始
					$con = "`begin_time` > $now ";
				else if($word == F3::get('EVENT_IS_RUNNING'))   //进行中
					$con = "`begin_time` < $now AND `end_time` > $now";
				else  //已结束
					$con = "`end_time` < $now ";
				$data = array();
				//$note .= "时间状态:<strong>{$word}</strong>";
				break;
			case '':
				$con = "eid = :a ";
				break;
			default:
				$con = "eid = :a ";
		}

		//if($order != null && stripos($con, 'status') === false){
		if(stripos($con, 'status') === false){
			$con .= "AND event.status = :s ";
			$data[':s'] = F3::get("EVENT_PASSED_STATUS");
		}

		switch($order){
			case "begin":
				$con .= " ORDER BY event.begin_time DESC";
				break;
			case "post":
				$con .= " ORDER BY event.post_time DESC";
				break;
			case "praiser":
				$con .= " ORDER BY event.praiser_num DESC";
				break;
			case "joiner":
				$con .= " ORDER BY event.joiner_num DESC";
				break;
			default:
				$con .= " ORDER BY event.begin_time DESC";
				break;
		}

		$r = array('con'=>$con,'array'=>$data, 'note'=>$note);

		// 根据条件筛选(附加其他条件)
		return $this->filter($r);
	}

	function find(){

		$key = F3::get("GET.key");
		$word = F3::get("GET.word");
		$order = F3::get("GET.order");

		//如果搜索框神码都没输入，就在value里面放上"-"作为标识，可以解决很多问题
		$word = $word == '' ? '-' : $word;

		$data = $this->find_by($key, $word, $order);

		$url = "find/by?";
		foreach(F3::get("GET") as $key => $v)
			$url .= "{$key}={$v}&";

		//Code::dump($data);
		//Code::dump($url);
		$event = new SEEvent();
		$result_num = $event->show_by($url, $data['con'], $data['array'], 'events');

		$event->show_by("", '`event`.`status` = :e ORDER BY RAND() DESC',
			array(':e' => F3::get("EVENT_PASSED_STATUS")), 'guess_events', 5);

		$event->show_by("", '`event`.`status` = :e ORDER BY `post_time` DESC',
			array(':e' => F3::get("EVENT_PASSED_STATUS")), 'newst_events', 5);

		F3::set('note', $data['note']);
		F3::set('result_num', $result_num);
		$category = Category::get_all();
		F3::set("category", $category);
		//$per_page_show = F3::get('PER_PAGE_SHOW');
		$current_page = F3::get('GET.page') == null ? 0 : (F3::get('GET.page'));
		F3::set('current_page', $current_page);
		//$page_note = $
		F3::set("route", array("discover"));
		//Code::dump(F3::get('events'));
		echo Template::serve('find/result.html');
	}

	function show_find(){
		$category = Category::get_all();
		F3::set("category", $category);
		$event = new SEEvent();
		$event->show_by("", '`event`.`status` = :e ORDER BY RAND() DESC',
			array(':e' => F3::get("EVENT_PASSED_STATUS")), 'guess_events', 5);

		$event->show_by("", '`event`.`status` = :e ORDER BY `post_time` DESC',
			array(':e' => F3::get("EVENT_PASSED_STATUS")), 'newst_events', 5);
		//Code::dump(F3::get('guess_events'));
		F3::set("route", array("discover"));
		echo Template::serve('find/find.html');
	}


	function run()
	{
		$event = new SEEvent();

		$event->show_by("", '', array(), 'hot_events', 4);

		foreach(F3::get("INDEX_BLOCK") as $b){
			$event->show_by("", $b['con'].' = :c AND `event`.`status` = :e',
				array(':c' => $b['value'], ':e' => F3::get("EVENT_PASSED_STATUS")), 'event.'.$b["name"], 4);
		}

		$event->show_by("", '`event`.`status` = :e ORDER BY RAND() DESC',
			array(':e' => F3::get("EVENT_PASSED_STATUS")), 'guess_events', 5);

		$event->show_by("", '`event`.`status` = :e ORDER BY `post_time` DESC',
			array(':e' => F3::get("EVENT_PASSED_STATUS")), 'newst_events', 5);
		
		echo Template::serve('index.html');
	}

	function my()
	{
		F3::set("route", array("my"));
		$gay = new SECommon();
		$gay->my();
	}


	function show_login()
	{	
		$backurl = F3::get('GET.backurl');
		switch(F3::get('GET.auth'))
		{
			case F3::get('CAS_AUTH'):
				$name = CAS::login();
				$pwd = F3::get('DEFAULT_PWD');
				if(Account::exists($name) === false)  //首次通过CAS登录
				{
					echo $pwd;
					$group = F3::get('STUDENT_GROUP');
					$nickname = $name;
					Admin::add_user($name, $pwd, $group, $nickname);
					$first_login = "true";
				}
				break;
			case F3::get('CLUB_AUTH'):
				echo Template::serve('club/login.html');
				return;
			default:
				echo Template::serve('/login.html');
				return;
		}

		Account::login($name, $pwd);

		if($backurl == "")
		{
			if(isset($first_login))
				F3::reroute('/?first_login=true');
			else
				F3::reroute('/');
		}
		else
		{
			if(isset($first_login))
				F3::reroute($backurl."?first_login=true");
			else
				F3::reroute($backurl);
		}
	}

	function login()
	{
		$user_name = F3::get('POST.user_name');
		if($user_name == "")
		{
			echo "用户名不能为空";
			return;
		}
		$user_pwd = F3::get('POST.user_pwd');
		if($user_pwd == "")
		{
			echo "密码不能为空";
			return;
		}
		$user = Account::login($user_name, $user_pwd); 

		if($user === false)
			echo "用户名密码不正确";
		else 
			echo 0;
	}

	function logout()
	{
		Account::logout();
		if(Account::the_user_group() == F3::get('STUDENT_GROUP'))
			CAS::logout();
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
				echo "取消推($num)";
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
	function ajax_update_my_profile()
	{
		$info = F3::get('POST');
		$uid = Account::the_user_id();
		$group = Account::the_user_group();

		//社团 机构 客服 无法修改自己的名称
		if($group == F3::get("CLUB_GROUP") || $group == F3::get("ORG_GROUP") || $group == ("SERVICE_GROUP"))
			$info['nickname'] = Account::the_user_name();

		if(Account::update_user_info($uid, $info) === true) //false 昵称重复或者为空  true 更新成功
		{
			Account::update_cookie();
			echo "1";
		}
		else
			echo "0";
	}

	function ajax_get_my_profile()
	{
		$uid = Account::the_user_id();
		$group = Account::the_user_group();
		$data = Account::get_user_full_info($uid);

		F3::set('u',$data);
		echo Template::serve("$group/my_profile.html");
		//Code::dump( $data);
	}

	function show_user_info()
	{
//		Account::the_user_id();
		$uid = F3::get('PARAMS.userID');
		$u = Account::get_user_full_info($uid);
	//	Code::dump($u);
		F3::set('u',$u);
		echo Template::serve('student/show_profile.html');
	}
};

?>
