<?php
/**
 * SEEvent Class
 *
 * @package   Slimevent
 **/

/**
 * Action for Event
 */
class SEEvent{

	function __construct()
	{
		SECommon::set_unread_msg_num();
	}

	function create(){
		$d = SECommon::get_create_form_value();

		Code::dump($d);
		if($d === false)
			$this->show_create();
		else{
			Account::create_event($d);
			F3::reroute("/event/{$eid}");
		}
	}

	function joins() 
	{
		$uid = Account::the_user_id(); //这个是当前登录用户的id
		$eid = F3::get('PARAMS.eventID');  //这个是用户要参加的活动id

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
	}

	function show_edit(){
		echo Template::serve('event/edit.html');
	}

	function show_create(){
		$region = array(array('id'=>1,'name'=>"一校区"), array('id'=>2,'name'=>"二校区"));
		$category = array();

		SECommon::generate_select_option($region, 'region');
		SECommon::generate_select_option($category, 'category');

		echo Template::serve('event/create.html');
	}

	function show(){
		F3::set('subnav', true);
		F3::set('route', array('discover', 'intro'));
		$event = Event::show(F3::get('PARAMS.eventID'));
		//F3::set('event',$event[0]);

		echo Template::serve('event/event1.html');
	}

	function photos(){
		F3::set('subnav', true);
		F3::set('route', array('discover', 'photo'));

		echo Template::serve('event/photo.html');
	}
	function discussion(){
		F3::set('subnav', true);
		F3::set('route', array('discover', 'discussion'));

		echo Template::serve('event/discussion.html');
	}
};

?>
