<?php


/**
 * 管理员类
 * @package Slimevent
 */

class Admin	extends Service{

	/**
	 * 添加某用户基本信息
	 * @param $uid : 用户在users表里的id 
	 * @param $data : 信息关联数组
	 */
	static function add_user_info($uid,$group,$data)
	{
		$data['uid'] = $uid;

		switch($group)
		{
			case F3::get('STUDENT_GROUP'):
				$table = 'student';
				break;
			case F3::get('CLUB_GROUP'):
				$table = 'club';
				break;
			case F3::get('ORG_GROUP'):
				$table = 'org';
				break;
			case F3::get('SERVICE_GROUP'):
				$table = 'service';
				break;
			case F3::get('SERVICE_GROUP'):
				$table = 'admin';
				break;
		}

		$b = EDB::select($table, 'uid', $uid);

		if( count($b) == 0 )
			EDB::insert($table, $data);
	}

	/*
	 * 添加一个用户
	 * @param $name : 用户名 
	 * @param $pwd : 密码 
	 * @param $group : 用户组 
	 * @param $status : 用户状态(默认空为正常态)
	 * @return $uid : 新建用户的$id
	 */
	static function add_user($name, $pwd, $group, $status = "", $data)
	{
		if(self::exists($name) !== false)
			Sys::error(F3::get('USERS_NAME_SAME_CODE'),$name);

		if($status == "")
			$status = F3::get('NORMAL_STATUS');

		$sql = "INSERT INTO `users` (`name`,`pwd`,`group`,`status`) VALUES (:name, :pwd, :group, :status)";

		$r = DB::sql($sql, array(
			':name' => trim($name),
			':pwd' => self::encrypt_pwd($pwd),
			':group' => trim($group),
			':status' => trim($status)
			));

		$uid = DB::get_insert_id();

		self::add_user_info($uid,$group, $data);

		return $uid;
	}

	/**
	 * 修改任意用户密码
	 * @param $uid : 用户在users表里的id  
	 * @param $pwd : 新密码
	 */
	static function reset_user_pwd($uid, $pwd)
	{
		self::get_user($uid);
		$sql = "UPDATE `users` SET `pwd` = :pwd WHERE `id` = :uid";
		DB::sql($sql, array(':uid' => trim($uid), ':pwd' => self::encrypt_pwd($pwd)));
	}

	/**
	 * 修改某用户状态
	 * @param $uid : 用户在users表里的id 
	 * @param $status : 用户新状态
	 */
	static function change_user_status($uid, $status)
	{
		self::get_user($uid);
		$sql = "UPDATE `users` SET `status` = :status WHERE `id` = :uid";
		DB::sql($sql, array(':uid' => trim($uid), ':status' => trim($status)));
	}


	/**
	 * 修改某用户基本信息
	 * @param $uid : 用户在users表里的id 
	 * @param $data : 信息关联数组
	 */
	static function edit_user_info($uid, $data)
	{
		$u = self::get_user($uid);
		$group = $u['0']['group'];

		switch($group)
		{
			case F3::get('STUDENT_GROUP'):
				$table = 'student';
				break;
			case F3::get('CLUB_GROUP'):
				$table = 'club';
				break;
			case F3::get('ORG_GROUP'):
				$table = 'org';
				break;
			case F3::get('SERVICE_GROUP'):
				$table = 'service';
				break;
			case F3::get('SERVICE_GROUP'):
				$table = 'admin';
				break;
		}

		EDB::update($table, $data, 'uid', $uid);
	}

};

?>
