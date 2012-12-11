<?php
/**
 * Student
 *
 * @package   Slimevent
 **/

class SEStudent{

	function __construct(){
		SECommon::set_unread_msg_num();
	}



	function praise_event($eid)
	{
		$uid = Student::the_user_id();
		PraiseList::add($uid, $eid);
	}

	function un_praise_event($eid)
	{
		$uid = Student::the_user_id();
		PraiseList::remove($uid, $eid);
	}

}

?>
