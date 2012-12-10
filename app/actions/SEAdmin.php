<?php
/**
 * Admin
 *
 * @package   Slimevent
 **/

class SEAdmin{

	function __construct(){
		//SECommon::set_unread_msg_num();
		
		//if(Account::the_user_group != F3::get("SERVICE_GROUP"))
			//Sys::error(F3::get("INVALID_GROUP_CODE"), Account::the_user_id());
	}

	function show_audit(){
		echo Template::serve("admin/audit.html");
	}

}
