<?php

	/**
	 * 活动类
	 * @package Slimevent
	 */

class Event{

	/**
	 * 在event表里创建一个新活动
	 * @param $data : 活动信息关联数组
	 * @return bool
	 */
	static function create($data)
	{
		$r = EDB::insert('event',$data);

		if($r == 1)
			return true;
		else
			return false;
	}

	/**
	 * 根据eid得到活动信息
	 * @param $eid : 活动id
	 * @return 不存在返回false 存在返回关联数组
	 */
	static function show($eid)
	{
		$sql = "SELECT * FROM `event` WHERE `eid` = :eid";
		$r = DB::sql($sql, array(':eid' => $eid));

		if(empty($r))
			return false;
		else
			return $r['0'];
	}
};
?>
