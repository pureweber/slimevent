<?php
/**
 * Club
 *
 * @package   Slimevent
 **/

class SEClub{

	function __construct(){
		//if(Accounts::check_group("club") === false)
			//F3::reroute("/");
		SECommon::set_unread_msg_num();
	}

	function show_list(){
		F3::set("title", "社团管理");
		$uid = Account::the_user_id();
		SECommon::show_by("club", "`organizer` = :uid", array(":uid"=>$uid));
		echo Template::serve('club/list.html');
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

}


