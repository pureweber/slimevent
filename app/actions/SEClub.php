<?php
/**
 * Club
 *
 * @package   Slimevent
 **/

class SEClub extends SECommon{

	function __construct(){
		parent::__construct();

		//if(Accounts::check_group("club") === false)
			//F3::reroute("/");
		if(Account::the_user_group() != F3::get("CLUB_GROUP"))
			Sys::error(F3::get("INVALID_GROUP_CODE"), Account::the_user_id());

		$this->set_unread_msg_num();
	}

	function my(){
		F3::set("title", "个人中心");
		echo Template::serve("club/my.html");
	}


}


