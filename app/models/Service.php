<?php

/**
 * 客服类
 * @package Slimevent
 */

class Service extends Account{

	static function event_audit_pass($eid)
	{
		$data = array('status' => F3::get('EVENT_PASSED_STATUS'));
		Event::update($eid, $data);
	}

	static function event_audit_fail($eid)
	{
		$data = array('status' => F3::get('EVENT_FAILED_STATUS'));
		Event::update($eid, $data);
	}

	static function get_event_to_audit()
	{
		$status = F3::get('EVENT_AUDIT_STATUS');
		$r = EDB::select('event','status',$status);
		return $r;
	}
};

?>
