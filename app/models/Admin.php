<?php


/**
 * 管理员类
 * @package Slimevent
 */

class Admin	extends Service{

	/**
	 * 对新增用户数据进行合法检查
	 * @return array 返回处理后的合法用户数据
	 */
	private static function check_new_user($name, $pwd, $group, $nickname)
	{
		//密码允许前后有空白字符
		$name = trim($name);
		$group = trim($group);
		$nickname = trim($nickname);

		if(strlen($name) < intval(F3::get('MIN_NAME_LEN')) || strlen($name) > intval(F3::get('MAX_NAME_LEN')))
			Sys::error(F3::get('ILLEGAL_NAME_LEN'), $name);  //用户名长度不合法

		if(strlen($pwd) < intval(F3::get('MIN_PWD_LEN')) || strlen($pwd) > intval(F3::get('MAX_PWD_LEN')))
			Sys::error(F3::get('ILLEGAL_PWD_LEN'), $pwd);  //密码长度不合法

		if(strlen($nickname) < intval(F3::get('MIN_NICKNAME_LEN')) || strlen($nickname) > intval(F3::get('MAX_NICKNAME_LEN')))
			Sys::error(F3::get('ILLEGAL_NICKNAME_LEN'), $nickname);  //昵称长度不合法

		if(self::exists($name, $nickname) === true)
			Sys::error(F3::get('USERS_NAME_OR_NICKNAME_SAME_CODE'),array('name'=>$name, 'nickname'=>$nickname)); //用户名或者昵称已经存在

		switch($group)
		{
			case F3::get('STUDENT_GROUP'):
			case F3::get('CLUB_GROUP'):
			case F3::get('ORG_GROUP'):
			case F3::get('SERVICE_GROUP'):
			//默认系统只有一个管理员 管理员不能添加别的管理员
			//case F3::get('ADMIN_GROUP'):  
				break;
			default:
				Sys::error(F3::get('ILLEGAL_USER_GROUP'));  //不是合法的用户组
		}

		return array('name' => $name, 
					'nickname' => $nickname,
					'pwd' => self::encrypt_pwd($pwd), 
					'group' => $group, 
					'first_time' => time(),
					'last_time' => time(),
					'status' => F3::get('NORMAL_STATUS'));
	}

	/**
	 * 添加一个用户
	 * @param $name : 用户名 
	 * @param $pwd : 密码 
	 * @param $group : 用户组 
	 * @param $nickname : 昵称
	 * @return $uid : 新建用户的$id
	 */
	static function add_user($name, $pwd, $group, $nickname)
	{
		$data = self::check_new_user($name, $pwd, $group, $nickname);

		EDB::insert('users', $data);
		$uid = DB::get_insert_id();

		EDB::deleted($group, 'uid', $uid);
		EDB::insert($group, array('uid' => $uid));   //在相关联的用户表里插入新用户 

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
