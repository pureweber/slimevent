<?php
/**
 * Service
 *
 * @package   Slimevent
 **/

class SEService extends SECommon{

	function __construct(){
		parent::__construct();
		//$this->set_unread_msg_num();
		
		//$group = Account::the_user_group();
		if(Account::the_user_group() != F3::get("SERVICE_GROUP") 
			&& Account::the_user_group() != F3::get("ADMIN_GROUP"))
			Sys::error(F3::get("INVALID_GROUP_CODE"), Account::the_user_id());
	}

	function my(){
		F3::set("title", "个人中心");
		echo Template::serve("service/my.html");
	}

	function show_audit(){
		$this->show_by("audit", "`event`.`status` = :status", array(":status"=>F3::get("EVENT_AUDIT_STATUS")));
		echo Template::serve("audit.html");
	}

	function pass(){
		$eid = F3::get("POST.eid");
		Service::event_audit_pass($eid);
		echo "1";
		//F3::reroute("audit");
	}

	function fail(){
		$eid = F3::get("POST.eid");
		Service::event_audit_fail($eid);
		echo "1";
		//F3::reroute("audit");
	}

}
