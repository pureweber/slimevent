<?php

	/**
	 * 活动类
	 * @package Slimevent
	 */

class Event{

	/**
	 * 根据eid和得到基本的活动信息
	 * @param $eid : SQL查询条件
	 * @return array 关联数组
	 */
	static function get_basic_info($eid)
	{
		$sql = "SELECT eid, begin_time, end_time, organizer_id, 
				category_id, status, sign_up, post_time FROM `event`
				WHERE eid = :eid";

		$r = DB::sql($sql, array(":eid"=>$eid));

		if(count($r) == 0)
			Sys::error(F3::get('EVENT_NOT_EXIST_CODE'),-1);
		else
			return $r[0];
	}

	/**
	 * 根据eid得到详细的活动信息
	 * @param $eid : 活动id
	 * @param $status : 活动状态
	 * @return array 返回关联数组
	 */
	static function show($eid, $status = '')
	{
		$status = $status == '' ? F3::get("EVENT_PASSED_STATUS") : $status;

		$e = self::get("`eid` = :eid AND status = :status",
			array(":eid"=>$eid,":status"=>$status));
		return $e[0];
	}

	/**
	 * 根据eid和查询条件得到详细的活动信息
	 * @param $con : SQL查询条件
	 * @param $data : SQL查询条件值
	 * @return array 返回多维关联数组
	 */
	static function show_by($con, $data)
	{
		return self::get($con, $data);
	}

	/**
	 * 根据查询条件得到符合条件活动的数量
	 * @param $con : SQL查询条件
	 * @return int
	 */
	static function get_num($con = "`status` = 'passed'", $data = array())
	{
		$sql = "SELECT COUNT(*) FROM `event` WHERE {$con}";

		$r = DB::sql($sql, $data);
		//Code::dump($r);

		return $r[0]["COUNT(*)"];
	}

	/**
	 * 根据eid和查询条件得到活动信息，供show()和show_by()调用
	 * @param $con : SQL查询条件
	 * @param $data : SQL查询条件的值
	 * @return array 返回多维关联数组
	 */
	private static function get($con, $data = array())
	{
		$sql = "SELECT `event`.*, `category`.`name` AS 'category' FROM `event`,`category` 
					WHERE `category`.id = `event`.`category_id` AND {$con}";

		$r = DB::sql($sql, $data);

		if(count($r) == 0)
			Sys::error(F3::get('EVENT_NOT_EXIST_CODE'),$con);
		//else if(count($r) == 1)
			//$e = $r['0'];
		//else	
			//Sys::error(F3::get('DB_EVENT_EID_SAME_CODE'),$eid);
		foreach($r as &$row){
			$organizer = Account::get_user($row['organizer_id']);
			$row['organizer'] = $organizer[1]['name'];
			$row['joiners'] = 10;
			$row['praisers'] = 18;
		}

		return $r;
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
		//self::show($eid);
			
		$r = EDB::update('event',$data,'eid',$eid);

		if($r == 0)
			return false;
		else
			return true;
	}


};
?>
