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

	public $eid = false;

	function __construct()
	{
		SECommon::set_unread_msg_num();
		$this->eid = F3::get('PARAMS.eventID');  //活动id
	}

	function show_edit(){
		$info = Event::show($this->eid, F3::get("EVENT_DRAFT_STATUS"));

		$info = SECommon::format_time_to_show($info);

		$info['eid'] = $this->eid;
		$_POST = $info;

		$region = F3::get("REGION");
		$category = Category::get_all();
		SECommon::generate_select_option($region, 'region');
		SECommon::generate_select_option($category, 'category');

		echo Template::serve('event/edit.html');
	}

	function audit(){
		$this->save('auditing');
		F3::reroute('/club');
	}


	function draft(){
		$eid = $this->save('draft');
		if($eid !== false){
			echo $eid;
		}else{
		}
	}

	private function save($status){
		$d = SECommon::get_create_form_value();
		if(!is_array($d)){
			$this->show_create();
			return;
		}

		//Code::dump($d);

		$d['status'] = $status;

		if($d['eid'] != 0){
			$has_exist= true;
			$eid = $d['eid'];
		}else{
			$has_exist= false;
		}
		unset($d['eid']);
		
		if($has_exist){
			Account::edit_event($eid, $d);
		} else {
			$eid = Account::create_event($d);
		}
		return $eid;
		//$this->show_create();
		//F3::reroute("/event/{$eid}");
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

	function my()
	{
		$uid = AccouListItemnt::the_user_id(); //这个是当前登录用户的id
		echo "当前登录用户的id: ".$uid;
	}

	function show_create(){
		$region = F3::get("REGION");
		$category = Category::get_all();

		SECommon::generate_select_option($region, 'region');
		SECommon::generate_select_option($category, 'category');

		echo Template::serve('event/create.html');
	}

	function show(){
		F3::set('subnav', true);
		F3::set('route', array('discover', 'intro'));
		$event = Event::show($this->eid);
		$event = SECommon::format_info_to_show($event);

		//Code::dump($event);
		F3::set('e',$event);

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
