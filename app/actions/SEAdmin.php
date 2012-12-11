<?php
/**
 * Admin
 *
 * @package   Slimevent
 **/

class SEAdmin extends SECommon{

	function __construct(){
		//$this->set_unread_msg_num();
		
		//if(Account::the_user_group != F3::get("SERVICE_GROUP"))
			//Sys::error(F3::get("INVALID_GROUP_CODE"), Account::the_user_id());
	}

	function show_audit(){
		echo Template::serve("admin/audit.html");
	}

	function show_add_user()
	{
		echo Template::serve('admin/add_user.html');
	}
	function add_user()
	{
		$name = F3::get('POST.name');
		$pwd = F3::get('POST.pwd');
		$group = F3::get('POST.group');
		$nickname =  F3::get('POST.nickname');
		$uid = Admin::add_user($name, $pwd, $group, $nickname);
		echo $uid;
	}

}
