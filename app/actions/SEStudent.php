<?php
/**
 * Student
 *
 * @package   Slimevent
 **/

class SEStudent extends SECommon{

	function __construct(){
		$this->set_unread_msg_num();
	}

	function my(){
		F3::set("title", "个人中心");
		F3::set("time", time());

		$this->set_create_event_list();
		$this->set_join_event_list();

		echo Template::serve("student/my.html");
	}


	function set_join_event_list(){
		$uid = Account::the_user_id();
		$con = " `eid` IN ( SELECT `eid` FROM `join` WHERE `uid` = :uid  ORDER BY `join`.`time` DESC)";
		$this->show_by("my", $con, array(":uid"=>$uid), "join_events");
	}

	function praise_event($eid)
	{
		$uid = Student::the_user_id();
		PraiseList::add($uid, $eid);
	}

	function join_event($eid)
	{

	}
}

?>
