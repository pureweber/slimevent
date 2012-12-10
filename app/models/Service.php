<?php

/**
 * 客服类
 * @package Slimevent
 */

class Service extends Account{

	static function event_audit_pass($eid)
	{
		$e = Event::get_basic_info($eid);
		$data = array('status' => F3::get('EVENT_PASSED_STATUS'));

		if($e['status'] == F3::get('EVENT_AUDIT_STATUS'))
			Event::update($eid, $data);
		else
			Sys::error(F3::get('FAIL_AUDIT_CODE'),$eid);
	}

	static function event_audit_fail($eid)
	{
		$e = Event::get_basic_info($eid);
		$data = array('status' => F3::get('EVENT_FAILED_STATUS'));

		if($e['status'] === F3::get('EVENT_AUDIT_STATUS'))
			Event::update($eid, $data);
		else
			Sys::error(F3::get('FAIL_AUDIT_CODE'),$eid);
	}

	// 无用
	static function get_event_to_audit()
	{
		$status = F3::get('EVENT_AUDIT_STATUS');
		$r = EDB::select('event','status',$status);
		return $r;
	}
};

?>
