<?php
/**
 * Student
 *
 * @package   Slimevent
 **/

class SEStudent extends SECommon{

	function __construct(){
		parent::__construct();
		if(Account::the_user_group() != F3::get("STUDENT_GROUP"))
			Sys::error(F3::get("INVALID_GROUP_CODE"), Account::the_user_id());

		$this->set_unread_msg_num();
	}

	function my(){
		F3::set("title", "个人中心");
		echo Template::serve("student/my.html");
	}


	function set_join_event_list(){
		$uid = Account::the_user_id();
		$con = " `eid` IN ( SELECT `eid` FROM `join` WHERE `uid` = :uid  ORDER BY `join`.`time` DESC)";
		$this->show_by("my", $con, array(":uid"=>$uid), "join_events");
	}

	function praise_event()
	{
		$uid = Student::the_user_id();
		$eid = F3::get('POST.eid');

		if(PraiseList::add($uid, $eid) === false) //之前赞过 表示这次取消赞
		{
			PraiseList::remove($uid, $eid);
			$num = count(PraiseList::get_praise_user($eid));
			echo " 推一下($num)";
		}
		else
		{
			$num = count(PraiseList::get_praise_user($eid));
			echo " 取消推($num)";
		}
	}

	function join_event()
	{
		$uid = Student::the_user_id();
		$eid = F3::get('POST.eid');

		if(JoinList::add($uid, $eid) === false) //之前报过名 表示这次要取消报名
		{
			JoinList::remove($uid, $eid);
			$num = count(JoinList::get_join_user($eid));
			echo " 我要报名($num)";
		}
		else
		{
			$num = count(JoinList::get_join_user($eid));
			echo " 取消报名($num)";
		}
	}
}

?>
