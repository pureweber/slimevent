<?php

	/**
	 * 用户反馈信息
	 * @package Slimevent
	 */

class Feedback{


	/**
	 * 新增加一条用户反馈
	 * @param $content
	 * return bool true  : 成功
	 */
	static function add($content)
	{
		$uid = Account::is_login();
		if($uid === false)
			$uid = 0;
		$ip = $_SERVER['REMOTE_ADDR'];
		$sql = "INSERT INTO feedback (uid, content, ip) VALUES (:uid, :content, :ip)";
		$r = DB::sql($sql, array(':uid' => $uid, ':content' => $content, ':ip' => $ip));

		return true;
	}


	/**
	 * @param $eid
	 * @return array 用户赞活动记录关联数组
	 */
	static function get_feedback()
	{
		$sql = "SELECT * FROM `feedback` ORDER BY time DESC";
		return DB::sql($sql, array(':eid' => $eid));
	}

	/**
	 * 返回$uid用户赞过的所有活动信息(eid, time)
	 * @return array 用户赞活动记录关联数组
	 */
	static function get_praise_event($uid)
	{
		$sql = "SELECT * FROM `feedback` WHERE uid = :uid ORDER BY time DESC";
		return DB::sql($sql, array(':uid' => $uid));
	}

};

?>
