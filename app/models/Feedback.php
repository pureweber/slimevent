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
	 * @return int 反馈总数
	 */
	static function get_num()
	{
		//$sql = "SELECT * FROM `feedback`, `users` WHERE feedback.uid = users.id ORDER BY time DESC";
		$sql = "SELECT count(*) FROM `feedback`";
		$r = DB::sql($sql);
		return $r[0]['count(*)'];
	}


	/**
	 * @return array 关联数组
	 */
	static function get_all_feedback($page)
	{
		//$sql = "SELECT * FROM `feedback`, `users` WHERE feedback.uid = users.id ORDER BY time DESC";
		$per = F3::get('PER_PAGE_SHOW');
		$p = $page * $per;
		$sql = "SELECT * FROM `feedback` ORDER BY time DESC LIMIT {$p}, {$per};";
		return DB::sql($sql);
	}


};

?>
