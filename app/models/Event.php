<?php

	/**
	 * 活动类
	 * @package Slimevent
	 */

class Event{

	/**
	 * 根据eid得到活动信息
	 * @param $eid : 活动id
	 * @return array 返回关联数组
	 */
	static function show($eid)
	{
		$sql = "SELECT `event`.*, `category`.`name` AS 'category' FROM `event`,`category` WHERE `eid` = :eid AND `category`.id = `event`.`category_id`";

		$r = DB::sql($sql, array(':eid' => $eid));

		if(count($r) == 0)
			Sys::error(F3::get('EVENT_NOT_EXIST_CODE'),$eid);
		else if(count($r) == 1)
			$e = $r['0'];
		else	
			Sys::error(F3::get('DB_EVENT_EID_SAME_CODE'),$eid);

		$organizer = Account::get_user($r['0']['organizer_id']);
		$e['organizer'] = $organizer['1']['name'];

		return $e;
	}

	/**
	 * 在event表里创建一个新活动
	 * @param $data : 活动信息关联数组
	 * @return $eid : 新建活动的id
	 */
	static function create($data)
	{
		EDB::insert('event', $data);
		return DB::get_insert_id();
	}

	/**
	 * 根据eid更新活动信息
	 * @param $eid
	 * @param $data 需要更新信息的关联数组(请不要包含eid)
	 * @return true : 更新成功 false : 没有更新
	 */
	static function update($eid, $data)
	{
		self::show($eid);
			
		$r = EDB::update('event',$data,'eid',$eid);

		if($r == 0)
			return false;
		else
			return true;
	}


};
?>
