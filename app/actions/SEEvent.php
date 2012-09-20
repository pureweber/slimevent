<?php

class SEEvent{

   	function __construct()
	{
		if(Account::is_login() === FALSE)
			F3::reroute('/');
	}

	function create(){
		$id = Account::the_user_id(); //这个是当前登录用户的id
		//你需要修改下面的代码和models里Event.php里的createevent()函数,使得创建活动用户的id也存到event表里

		$a =Event::createevent(
			$_POST["title"],
			$_POST["sort"],
			$_POST["label"],
			$_POST["location"],
			$_POST["starttime"],
			$_POST["endtime"],
			$_POST["introduction"]
		);

		F3::reroute("/event/{$a}");
	}

	function joins() 
	{
		$uid = Account::the_user_id(); //这个是当前登录用户的id
		$eid = F3::get('PARAMS.eventID');  //这个是用户要参加的活动id

		/*想办法获取下面两个时间*/
		//$spare_time_start =   //用户空闲的开始时间
		//$spare_time_end =     //用户空闲的结束时间 

		//之后你要做得就是把上现的参数传到models里的某个静态函数里,在那个静态函数里把这些参数存入数据库

		echo "当前登录用户的id: ".$uid;
		echo "用户要参加活动的id: ".$eid;
	}
	function participants()
	{
		$eid = F3::get('PARAMS.eventID');  //这个是用户要参加的活动id
		echo "参加活动的id: ".$eid;
		//显示id为eid活动的所有参与者信息(名字,起始空闲时间)
	}
	function my()
	{
		$uid = Account::the_user_id(); //这个是当前登录用户的id
		echo "当前登录用户的id: ".$uid;
		
		//根据uid从数据库搜索出该用户创建的所有活动以及参与的所有活动的信息
		//之后在ui目录下创建一个html文件,用于显示当前用户创建活动以及参与活动的列表

		/*用于用户可能参与或创建了很多活动,不可能用一个个独立的变量把活动的信息传到html页面,所以你需要使用数组
		最好的是,给html页面只传递两个数组,分别存储着创建的所有活动信息以及参与的所用活动信息
		*/
	}

	function show_create(){
		echo Template::serve('create.html');
	}

	function show(){
		F3::set('route', array('discover', 'intro'));
		$event = Event::getevent(F3::get('PARAMS.eventID'));
		F3::set('event',$event[0]);

		echo Template::serve('event/event.html');
	}

	function photos(){
		F3::set('route', array('discover', 'photo'));

		echo Template::serve('event/photo.html');
	}
	function discussion(){
		F3::set('route', array('discover', 'discussion'));

		echo Template::serve('event/discussion.html');
	}
};

?>
