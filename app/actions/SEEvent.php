<?php
/**
 * SEEvent Class
 *
 * @package   Slimevent
 **/

/**
 * Action for Event
 */
class SEEvent extends SECommon{

	public $eid = false;

	function __construct()
	{
		$this->set_unread_msg_num();
		$this->eid = F3::get('PARAMS.eventID');  //活动id
	}

	function show_edit(){
		$info = Event::show($this->eid, false);

		$info = $this->format_time_to_show($info);

		$info['eid'] = $this->eid;
		$_POST = $info;

		$region = F3::get("REGION");
		$category = Category::get_all();
		$this->generate_select_option($region, 'region');
		$this->generate_select_option($category, 'category');

		echo Template::serve('event/edit.html');
	}

	function del(){
		Account::delete_event($this->eid);
		F3::reroute('/my');
	}

	function publish(){
		$d = $this->get_create_form_value();
		$eid = $this->get_eid_if_exist($d);

		if($eid !== false){
			Account::edit_event($eid, $d);
		} else {
			$eid = Account::create_event($d);
		}
		Account::publish_event($eid);

		F3::reroute('/my');
	}


	function draft(){
		$d = $this->get_create_form_value();
		$eid = $this->get_eid_if_exist($d);
		if($eid !== false){
			Account::edit_event($eid, $d);
		} else {
			$eid = Account::create_event($d);
		}

		echo $eid;
	}

	private function get_eid_if_exist(&$d){
		if($d['eid'] != 0)
			$eid = $d['eid'];
		else
			$eid = false;
		
		unset($d['eid']);

		return $eid;
	}


	function joins() 
	{
		$uid = Account::the_user_id(); //这个是当前登录用户的id

		echo "当前登录用户的id: ".$uid;
		echo "用户要参加活动的id: ".$eid;
	}

	function participants()
	{
		$eid = F3::get('PARAMS.eventID');  //这个是用户要参加的活动id
		echo "参加活动的id: ".$eid;
		//显示id为eid活动的所有参与者信息(名字,起始空闲时间)
	}

	function my_event()
	{
		$uid = AccouListItemnt::the_user_id(); //这个是当前登录用户的id
		echo "当前登录用户的id: ".$uid;
	}

	function show_create(){
		$region = F3::get("REGION");
		$category = Category::get_all();

		$this->generate_select_option($region, 'region');
		$this->generate_select_option($category, 'category');

		echo Template::serve('event/create.html');
	}

	function show_join_list(){
		F3::set("title", "已报名列表");
		$data = array();
		$data[] = array(
			'uid'=>1,
			'date'=>"2012/12/24",
			);
		F3::set('list', $data);
		echo Template::serve('club/join.html');
	}

	function show($status = true){
		F3::set('subnav', true);
		F3::set('route', array('discover', 'intro'));
		$event = Event::show($this->eid, $status);
		$event = $this->format_info_to_show($event);

		//Code::dump($event);
		F3::set('e',$event);

		echo Template::serve('event/event1.html');
	}

	function preview(){
		if(Account::preview_event($this->eid))
			$this->show(false);
		else
			Sys::error(F3::get("EVENT_NOT_PREIVEW"), $this->eid);
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
