<?php

	/**
	 * 用户报名参加活动表
	 * @package Slimevent
	 */

class JoinList{

	/**
	 * 检查活动是否能允许报名或者取消报名
	 * @param $eid
	 */
	private static function check($eid)
	{
		$e = Event::get_basic_info($eid);  //得到活动详情

		if($e['sign_up'] == F3::get('EVENT_NEED_SIGN') && $e['status'] == F3::get('EVENT_PASSED_STATUS'))  //活动允许报名且活动为审核通过状态
		{
			if($e['end_time'] < time())
				Sys::error(F3::get('EVENT_SIGN_EXPIRED'),$eid);  //活动已结束
		}
		else
			Sys::error(F3::get('EVENT_NOT_SIGN'),$eid);		//活动不允许报名
	}

	/**
	 * 新增加一条用户报名活动的记录
	 * @param $uid 
	 * @param $eid
	 */
	static function add($uid, $eid)
	{
		self::check($eid);

		$sql = "SELECT * FROM `join` WHERE `uid` = :uid AND `eid` = :eid";
		$r = DB::sql($sql, array(':uid' => $uid, ':eid' => $eid));
		if(count($r) > 0)
			Sys::error(F3::get('HAVE_SIGNED'),$eid);

		$sql = 'INSERT INTO `join` (`uid`, `eid`, `time`) VALUES (:uid, :eid, :time)';
		DB::sql($sql, array(':uid' => $uid, ':eid' => $eid, ':time' => time()));
	}

	/**
	 * 删除一条用户报名活动的记录
	 * @param $uid 
	 * @param $eid
	 */
	static function remove($uid, $eid)
	{
		self::check($eid);
		$sql = "DELETE FROM `join` WHERE `uid` = :uid AND `eid` = :eid";
		DB::sql($sql, array(':uid' => $uid, ':eid' => $eid));
	}

	/**
	 * 返回报名$eid活动的用户信息(uid, time)
	 * @param $eid
	 * @return array 用户的报名活动记录关联数组
	 */
	static function get_join_user($eid)
	{
		$sql = "SELECT `uid`, `time` FROM `join` WHERE `eid` = :eid";
		return DB::sql($sql, array(':eid' => $eid));
	}

	/*
	 * 返回$uid用户报名过的所有活动信息(eid, time)
	 * @return array 用户的报名活动记录关联数组
	 */
	static function get_join_event($uid)
	{
		$sql = "SELECT `eid`, `time` FROM `join` WHERE `uid` = :uid";
		return DB::sql($sql, array(':uid' => $uid));
	}
};

?>
