<?php

/** 客服类
 * @package Slimevent
 */

class Service extends Account{

	/**
	 * 审核通过eid活动
	 * @param int $eid
	 */
	static function event_audit_pass($eid)
	{
		$e = Event::get_basic_info($eid);
		$uid = Account::the_user_id();

		if($e['status'] == F3::get('EVENT_PASSED_STATUS'))
			return;

		//能把等待审核 未通过审核的 改变为审核通过
		if($e['status'] == F3::get('EVENT_AUDIT_STATUS') || $e['status'] == F3::get('EVENT_FAILED_STATUS'))  
		{
			if($e['old_id'] == F3::get('NO_OLD_ID'))
			{
				Event::update($eid, array('status' => F3::get('EVENT_PASSED_STATUS')));
				AuditList::create($eid,$uid,F3::get('EVENT_PASSED_STATUS'));
			}
			else  //它是一个儿子版本 需要把父亲内容替换为自己
			{
				$data = array(
					'title' => $e['title'],
					'region' => $e['region'],
					'addr' => $e['addr'],
					'begin_time' => $e['begin_time'],
					'end_time' => $e['end_time'],
					'category_id' => $e['category_id'],
					'label' => $e['label'],
					'poster' => $e['poster'],
					'introduction' => $e['introduction'],
					'sign_up' => $e['sign_up']
				);
				Event::update($e['old_id'],$data); //更新父亲内容自己
				Event::deleted($eid);	//删除儿子版本
			}
		}
		else
			Sys::error(F3::get('FAIL_AUDIT_CODE'),$eid);
	}

	/**
	 * 审核不通过eid活动
	 * @param int $eid
	 */
	static function event_audit_fail($eid, $reason)
	{
		$e = Event::get_basic_info($eid);
		$uid = Account::the_user_id();

		if($e['status'] == F3::get('EVENT_FAILED_STATUS'))
			return;

		//能把等待审核 通过审核的 改变为未审核通过
		if($e['status'] === F3::get('EVENT_AUDIT_STATUS') || $e['status'] === F3::get('EVENT_PASSED_STATUS'))
		{
			Event::update($eid, array('status' => F3::get('EVENT_FAILED_STATUS')));
			AuditList::create($eid,$uid,F3::get('EVENT_FAILED_STATUS'),$reason);
		}
		else
			Sys::error(F3::get('FAIL_AUDIT_CODE'), $eid);
	}

	/**
	 * 获取所有待审核的活动信息
	 * @return array
	 */
	static function get_event_to_audit()
	{
		$status = F3::get('EVENT_AUDIT_STATUS');
		$r = EDB::select('event','status',$status);
		return $r;
	}

};

?>
