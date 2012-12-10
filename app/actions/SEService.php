<?php
/**
 * Service
 *
 * @package   Slimevent
 **/

class SEService{

	function __construct(){
		//SECommon::set_unread_msg_num();
		
		//$group = Account::the_user_group();
		//if($group != F3::get("SERVICE_GROUP") 
			//&& $group != F3::get("ADMIN_GROUP"))
			//Sys::error(F3::get("INVALID_GROUP_CODE"), Account::the_user_id());
	}

	function show_audit(){
		SECommon::show_by("audit", "`status` = :status", array(":status"=>F3::get("EVENT_AUDIT_STATUS")));
		echo Template::serve("service/audit.html");
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
