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
		parent::__construct();
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
		$this->generate_select_option($category, 'category_id');

		F3::set("title1", "编辑活动");
		echo Template::serve('event/edit.html');
	}

	function del(){
		Account::delete_event($this->eid);
		F3::reroute('/my');
	}

	function ajax_handle_event()
	{
		$eid = F3::get('POST.eid');
		$type = F3::get('POST.type');

		if($type == F3::get('HANDLE_DEL'))
			Account::delete_event($eid);
		else if($type == F3::get('HANDLE_PUB'))
			Account::publish_event($eid);
		else if($type == F3::get('HANDLE_PAS'))
			Service::event_audit_pass($eid);
		else if($type == F3::get('HANDLE_FAIL'))
			Service::event_audit_fail($eid,F3::get('POST.reason'));
		else if($type == F3::get('HANDLE_UNJOIN'))  //取消参加
			JoinList::remove(Account::the_user_id(), $eid);
		else if($type == F3::get('HANDLE_UNPRAISE'))  //取消推
			PraiseList::remove(Account::the_user_id(), $eid);
	}

	function ajax_my_event_list()
	{
		$uid = Account::the_user_id();
		$status = F3::get('POST.type');
		$group = Account::the_user_group();

		switch($status)
		{
			case F3::get('EVENT_DRAFT_STATUS'):
				$events = Event::get_draft_event_list($uid);
				break;
			case F3::get('EVENT_AUDIT_STATUS'):
				$events = Event::get_auditing_event_list($uid);
				break;
			case F3::get('EVENT_PASSED_STATUS'):
				$events = Event::get_passed_event_list($uid);
				break;
			case F3::get('EVENT_FAILED_STATUS'):
				$events = Event::get_failed_event_list($uid);
				break;
			case F3::get('EVENT_DELETED_STATUS'):
				$events = Event::get_delete_event_list($uid);
				break;
			case F3::get('EVENT_JOIN_STATUS'):
				$events = Event::get_join_event_list($uid);
				break;
			case F3::get('EVENT_PRAISE_STATUS'):
				$events = Event::get_praise_event_list($uid);
				break;
			default:
				$events = array();
		}

		$e = $this->format_infos_to_show($events);
		F3::set('events', $e);
		echo Template::serve("$group/$status"."_list.html");
	}

	function publish(){
		$d = $this->get_create_form_value();
		$eid = $this->get_eid_if_exist($d);

		if($eid !== false){
			$eid = Account::edit_event($eid, $d);
		} else {
			$eid = Account::create_event($d);
		}
		$eid = Account::publish_event($eid);

		F3::reroute("/event/$eid");
	}


	function draft(){
		$d = $this->get_create_form_value();
		$eid = $this->get_eid_if_exist($d);
		if($eid !== false){
			$eid =Account::edit_event($eid, $d);
		} else {
			$eid = Account::create_event($d);
		}

		echo $eid;
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

	function show_create(){
		$uid = Account::the_user_id();
		F3::set("title", "创建活动");
		$region = F3::get("REGION");
		$category = Category::get_all();

		$this->generate_select_option($region, 'region');
		$this->generate_select_option($category, 'category_id');

		F3::set("title1", "创建活动");
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

	function show()
	{
		F3::set('subnav', true);
		F3::set('route', array('discover', 'intro'));
		$event = Account::view_one_event($this->eid);
		$event = $this->format_info_to_show($event);
		F3::set('e',$event);

		$uid = $event['organizer_id'];
		$label = $event['label'];

		if(count($label) == 0)
			$r = 0;
		else
		{
			$con = "`event`.`status` = :e AND `event`.`eid` <> $this->eid AND ( `event`.`label` LIKE '%$label[0]%' ";
			foreach($label as $l)
				$con .= "OR `event`.`label` LIKE '%$l%' ";
			$con .= ") ORDER BY `post_time` DESC";

			$r = $this->show_by("",$con,array(':e' => F3::get("EVENT_PASSED_STATUS")), 'related_events', 5);
		}

		if($r == 0)
			$this->show_by("", '`event`.`status` = :e ORDER BY RAND() DESC',
				array(':e' => F3::get("EVENT_PASSED_STATUS")), 'related_events', 5);

		$this->show_by("","`event`.`status` = :e AND `event`.`organizer_id` = '$uid' ORDER BY `post_time` DESC",
			array(':e' => F3::get("EVENT_PASSED_STATUS")), 'other_events', 5);
		$this->show_by("", '`event`.`status` = :e ORDER BY RAND() DESC',
			array(':e' => F3::get("EVENT_PASSED_STATUS")), 'guess_events', 5);
		//Code::dump($event);
		echo Template::serve('event/event1.html');
	}

	/*function preview(){*/
		//if(Account::preview_event($this->eid))
			//$this->show(false);
		//else
			//Sys::error(F3::get("EVENT_NOT_PREIVEW"), $this->eid);
	/*}*/

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

	private function get_eid_if_exist(&$d){
		if($d['eid'] != 0)
			$eid = $d['eid'];
		else
			$eid = false;
		
		unset($d['eid']);

		return $eid;
	}

	function show_event_join_list()
	{
		$eid = F3::get('PARAMS.eventID');  //活动id

		if(Account::verify_handle_event_permission($eid) === false)
			Sys::error(F3::get("NO_PERMISSION_SEE_JOIN_LIST"), $eid);

		$e = Event::get_basic_info($eid);
		$u = JoinList::get_join_user($eid);
		F3::set("u",$u);
		F3::set("e",$e);
		echo Template::serve('event/join_list.html');
	}

};

?>
