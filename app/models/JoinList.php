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
	 * @return bool true : 保命成功  false: 之前报过名
	 */
	static function add($uid, $eid)
	{
		self::check($eid);

		$sql = "SELECT * FROM `join` WHERE `uid` = :uid AND `eid` = :eid";
		$r = DB::sql($sql, array(':uid' => $uid, ':eid' => $eid));
		if(count($r) > 0)
			//Sys::error(F3::get('HAVE_SIGNED'),$eid);
			return false;

		$sql = 'INSERT INTO `join` (`uid`, `eid`, `time`) VALUES (:uid, :eid, :time)';
		DB::sql($sql, array(':uid' => $uid, ':eid' => $eid, ':time' => time()));

		$sql = 'UPDATE `event` SET joiner_num=joiner_num+1 WHERE eid = :eid';
		DB::sql($sql, array(':eid' => $eid));

		return true;
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
		$sql = 'UPDATE `event` SET joiner_num=joiner_num-1 WHERE eid = :eid AND joiner_num >= 0';
		DB::sql($sql, array(':eid' => $eid));
	}

	/**
	 * 返回报名$eid活动的用户信息(uid, time)
	 * @param $eid
	 * @return array 用户的报名活动记录关联数组
	 */
	static function get_join_user($eid)
	{

		$sql = "SELECT `uid`,`time`, `nickname`,`name` FROM `join`,`users` WHERE `eid` = :eid AND `users`.`id` = `join`.`uid` ORDER BY `time` ASC";
		$r = DB::sql($sql, array(':eid' => $eid));

		foreach($r as &$v)
			$v['time'] = date("Y-m-d H:i:s", $v['time']);

		return $r;
	}

	/**
	 * 返回$uid用户报名过的所有活动信息(eid, time)
	 * @return array 用户的报名活动记录关联数组
	 */
	static function get_join_event($uid)
	{
		$con = " `eid` IN ( SELECT `eid` FROM `join` WHERE `uid` = :uid  ORDER BY `join`.`time` DES)";
		return Event::show_by($con, array(':uid' => $uid));
		//$sql = "SELECT `eid`, `time` FROM `join` WHERE `uid` = :uid";
		//return DB::sql($sql, array(':uid' => $uid));
	}

	/**
	 * 判断用户uid是否参加了eid活动
	 * @param $uid
	 * @param $eid
	 * @return bool
	 */
	static function is_user_join_event($uid, $eid)
	{
		$sql = "SELECT * FROM `join` WHERE `uid` = :uid AND `eid` = :eid";
		$r = DB::sql($sql, array(':uid' => $uid, ':eid' => $eid));

		if(count($r) > 0)
			return true;
		else
			return false;
	}
};

?>
