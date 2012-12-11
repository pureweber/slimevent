<?php
/**
 * Club
 *
 * @package   Slimevent
 **/

class SEClub extends SECommon{

	function __construct(){
		//if(Accounts::check_group("club") === false)
			//F3::reroute("/");
		$this->set_unread_msg_num();
	}

	function my(){
		F3::set("title", "社团管理");
		$this->set_created_event_list();
		echo Template::serve("club/my.html");
	}


}


