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
		$sql = "SELECT * FROM `event` WHERE eid = :eid";

		$r = DB::sql($sql, array(":eid"=>$eid));

		if(count($r) == 0)
			Sys::error(F3::get('EVENT_NOT_EXIST_CODE'),$eid);
		else
			return $r[0];
	}
	
	/**
	 * 根据eid得到详细的活动信息
	 * @param $eid : 活动id
	 * @param $status : 活动状态
	 * @return array 返回关联数组
	 */
	static function show($eid, $status = true)
	{
		if($status){
			$con = "`eid` = :eid AND `event`.`status` = :status";
			$d = array(":eid"=>$eid, ":status"=>F3::get("EVENT_PASSED_STATUS"));
		}else{
			$con = "`eid` = :eid";
			$d = array(":eid"=>$eid);
		}

		$e = self::get($con, $d);

		if(count($e) == 0)
			Sys::error(F3::get('EVENT_NOT_EXIST_CODE'),$eid);
		else
			return $e[0];
	}

	/**
	 * 根据eid和查询条件得到详细的活动信息
	 * @param $con : SQL查询条件
	 * @param $data : SQL查询条件值
	 * @return array 返回多维关联数组
	 */
	static function show_by($con, $data = array())
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

		if(stripos($con, 'group by') === false)// 不包含group by
			return $r[0]["COUNT(*)"];
		else
			return count($r);
	}

	/**
	 * 根据eid和查询条件得到活动信息，供show()和show_by()调用
	 * @param $con : SQL查询条件
	 * @param $data : SQL查询条件的值
	 * @return array 返回多维关联数组
	 */
	private static function get($con, $data = array())
	{
		$sql = "SELECT `event`.*, `category`.`name` AS 'category',`users`.`nickname` AS 'organizer'
					FROM `event`,`category`,`users`
					WHERE `category`.id = `event`.`category_id` AND `users`.`id` = `event`.`organizer_id`
					AND {$con}";

		$r = DB::sql($sql, $data);

			//$row['praisers'] = count(PraiseList::get_praise_user($row['eid']));

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
		$r = EDB::update('event',$data,'eid',$eid);

		if($r == 0)
			return false;
		else
			return true;
	}

	/** 
	 * 根据完整的活动信息关联数组$data生成它的儿子版本
	 * @param $data array
	 * @return int 返回它的儿子eid
	 */
	static function backup($data)
	{
		$data['old_id'] = $data['eid'];
		$data['post_time'] = time();
		unset($data['eid']);
		return Event::create($data);
	}

	/**
	 * 根据eid返回它的儿子版本eid
	 * @param int $eid
	 * @return int false(没有儿子)
	 */
	static function get_backup($eid)
	{
		$sql = "SELECT * FROM `event` WHERE `old_id` = :eid";
		$r = DB::sql($sql, array(':eid' => $eid));

		//Code::dump($r);

		if(count($r) == 0)
			return false;
		else if(count($r) == 1)
			return $r[0]['eid'];
		else
			Sys::error(F3::get('EVENT_HAS_MORE_BACKUP'),$eid);
	}

	/**
	 * 删除eid活动
	 */
	static function deleted($eid)
	{
		$sql = "DELETE FROM `event` WHERE `eid` = :eid";
		DB::sql($sql, array(':eid' => $eid));
	}

	/**
	 * 获取用户$uid 对应的草稿活动列表
	 * @param $uid 
	 * @return array 活动信息的二维关联数组
	 */
	static function get_draft_event_list($uid)
	{
		$user =  Account::get_user($uid);
		$group = $user['group'];

		//学生 社团 机构 对应的是自己的草稿
		if($group == F3::get('STUDENT_GROUP') || $group == F3::get('CLUB_GROUP') || $group == F3::get('ORG_GROUP'))
			$con = "`organizer_id` = '$uid' AND `event`.`status` = '".F3::get('EVENT_DRAFT_STATUS') ."' ORDER BY post_time DESC"; 
		//管理员对应的是系统内的所有草稿
		else if($group == F3::get('ADMIN_GROUP'))
			$con = "`event`.`status` = '".F3::get('EVENT_DRAFT_STATUS') ."' ORDER BY post_time DESC"; 
		//客服其他人员 没有对应草稿类别
		else
			return array();

		return self::get($con);
	}

	/**
	 * 获取用户$uid 对应的待审核活动列表
	 * @param $uid 
	 * @return array 活动信息的二维关联数组
	 */
	static function get_auditing_event_list($uid)
	{
		$user =  Account::get_user($uid);
		$group = $user['group'];

		//学生 社团 机构 对应的是自己的待审核的活动 
		if($group == F3::get('STUDENT_GROUP') || $group == F3::get('CLUB_GROUP') || $group == F3::get('ORG_GROUP'))
			$con = "`organizer_id` = '$uid' AND  `event`.`status` = '".F3::get('EVENT_AUDIT_STATUS') ."' ORDER BY post_time DESC"; 
		//客服 管理员 对应的是系统内所有待审核的列表
		else if($group == F3::get('ADMIN_GROUP') || $group == F3::get('SERVICE_GROUP'))
			$con = "`event`.`status` = '".F3::get('EVENT_AUDIT_STATUS') ."' ORDER BY post_time DESC"; 
		//其他人员 无对应待审核列表
		else
			return array();

		return self::get($con);
	}

	/**
	 * 获取用户$uid 对应的审核通过活动列表
	 * @param $uid 
	 * @return array 活动信息的二维关联数组
	 */
	static function get_passed_event_list($uid)
	{
		$user =  Account::get_user($uid);
		$group = $user['group'];

		//学生 社团  机构 对应的是自己被通过的活动列表
		if($group == F3::get('STUDENT_GROUP') || $group == F3::get('CLUB_GROUP') || $group == F3::get('ORG_GROUP'))
			$con = "`organizer_id` = '$uid' AND  `event`.`status` = '".F3::get('EVENT_PASSED_STATUS') ."' ORDER BY post_time DESC"; 
		//客服 对应的是自己批准的活动列表
		else if( $group == F3::get('SERVICE_GROUP'))
		{
			//$con = "`event`.`status` = '".F3::get('EVENT_PASSED_STATUS') ."' ORDER BY post_time DESC"; 
			$sql = "SELECT `event`.*,
						`audit`.`comments`,
						`audit`.`time` AS 'audit_time', 
						`users`.`nickname` AS 'organizer'
					FROM `event`,`users`,`audit` WHERE 
					`users`.`id` = `event`.`organizer_id` AND
					`event`.`eid` = `audit`.`eid` AND 
					`audit`.`uid` = '$uid' AND 
					`event`.`status` = '".F3::get('EVENT_PASSED_STATUS') ."' ORDER BY post_time DESC"; 
			return DB::sql($sql);
		}
		//管理员 对应的是系统内所有被批准的活动列表
		else if($group == F3::get('ADMIN_GROUP'))
		{
			$sql = "SELECT `event`.*,
						`audit`.`uid` AS 'audit_user', 
						`audit`.`comments`,
						`audit`.`time` AS 'audit_time', 
						`users`.`nickname` AS 'organizer'
					FROM `event`,`users`,`audit` WHERE 
					`users`.`id` = `event`.`organizer_id` AND
					`event`.`eid` = `audit`.`eid` AND 
					`event`.`status` = '".F3::get('EVENT_PASSED_STATUS') ."' ORDER BY post_time DESC"; 

			return DB::sql($sql);
		}
		else
			return array();

		return self::get($con);
	}

	/**
	 * 获取用户$uid 对应的未通过审核的活动列表
	 * @param $uid 
	 * @return array 活动信息的二维关联数组
	 */
	static function get_failed_event_list($uid)
	{
		$user =  Account::get_user($uid);
		$group = $user['group'];

		//学生 社团 机构 对应的是自己被未通过的活动列表
		if($group == F3::get('STUDENT_GROUP') || $group == F3::get('CLUB_GROUP') || $group == F3::get('ORG_GROUP'))
		{
			//$con = "`organizer_id` = '$uid' AND  `event`.`status` = '".F3::get('EVENT_FAILED_STATUS') ."' ORDER BY post_time DESC"; 

			$sql = "SELECT `event`.*, `audit`.`comments`, `audit`.`time` AS 'audit_time' FROM `event`,`audit` WHERE 
					`event`.`eid` = `audit`.`eid` AND 
					`organizer_id` = '$uid' AND 
					`event`.`status` = '".F3::get('EVENT_FAILED_STATUS') ."' ORDER BY post_time DESC"; 
			return DB::sql($sql);
		}
		//客服 对应的是自己不批准的活动列表
		else if( $group == F3::get('SERVICE_GROUP'))
		{
			$sql = "SELECT `event`.*,
						`audit`.`comments`,
						`audit`.`time` AS 'audit_time', 
						`users`.`nickname` AS 'organizer'
					FROM `event`,`users`,`audit` WHERE 
					`users`.`id` = `event`.`organizer_id` AND
					`event`.`eid` = `audit`.`eid` AND 
					`audit`.`uid` = '$uid' AND 
					`event`.`status` = '".F3::get('EVENT_FAILED_STATUS') ."' ORDER BY post_time DESC"; 
			return DB::sql($sql);
		}
		//管理员 对应的是系统内所有未通过审核的活动列表
		else if($group == F3::get('ADMIN_GROUP'))
		{
			$sql = "SELECT `event`.*,
						`audit`.`uid` AS 'audit_user', 
						`audit`.`comments`,
						`audit`.`time` AS 'audit_time', 
						`users`.`nickname` AS 'organizer'
					FROM `event`,`users`,`audit` WHERE 
					`users`.`id` = `event`.`organizer_id` AND
					`event`.`eid` = `audit`.`eid` AND 
					`event`.`status` = '".F3::get('EVENT_FAILED_STATUS') ."' ORDER BY post_time DESC"; 

			return DB::sql($sql);
		}
		else
			return array();
		
		return self::get($con);
	}

	/**
	 * 获取用户$uid 对应的删除的活动列表
	 * @param $uid 
	 * @return array 活动信息的二维关联数组
	 */
	static function get_delete_event_list($uid)
	{
		$user =  Account::get_user($uid);
		$group = $user['group'];

		//管理员 对应的是系统内所有删除的活动列表
		if($group == F3::get('ADMIN_GROUP'))
			$con = "`event`.`status` = '".F3::get('EVENT_DELETED_STATUS') ."' ORDER BY post_time DESC"; 
		//其他用户 没有对应的删除列表
		else
			return array();

		return self::get($con);
	}

	/**
	 * 获取用户$uid 对应的参加的活动列表
	 * @param $uid 
	 * @return array 活动信息的二维关联数组
	 */
	static function get_join_event_list($uid)
	{
		$user =  Account::get_user($uid);
		$group = $user['group'];

		//学生 对应的是自己参加的活动列表
		if($group == F3::get('STUDENT_GROUP'))
			$con = "`event`.`status` = '".F3::get('EVENT_PASSED_STATUS')."' AND `event`.`eid` IN ( SELECT `eid` FROM `join` WHERE `uid` = '$uid')"; 
		//其他用户 没有对应的参加活动的列表
		else
			return array();

		return self::get($con);

	}

	/**
	 * 获取用户$uid 对应的赞的活动列表
	 * @param $uid 
	 * @return array 活动信息的二维关联数组
	 */
	static function get_praise_event_list($uid)
	{
		$user =  Account::get_user($uid);
		$group = $user['group'];

		//学生 对应的是自己赞的活动列表
		if($group == F3::get('STUDENT_GROUP'))
			$con = "`event`.`status` = '".F3::get('EVENT_PASSED_STATUS')."' AND `event`.`eid` IN ( SELECT `eid` FROM `praise` WHERE `uid` = '$uid')"; 
		//其他用户 没有对应的赞活动的列表
		else
			return array();

		return self::get($con);
	}

};
?>
