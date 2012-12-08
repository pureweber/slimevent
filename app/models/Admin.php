<?php


/**
 * 管理员类
 * @package Slimevent
 */

class Admin	extends Service{

	/*
	 * 添加一个用户
	 * @param $name : 用户名 
	 * @param $pwd : 密码 
	 * @param $group : 用户组 
	 * @param $status : 用户状态(默认空为正常太)
	 * @return bool 成功返回true 失败返回false
	 */
	static function add_user($name, $pwd, $group, $status = "")
	{
		if($status == "")
			$status = F3::get('NORMAL_STATUS');

		$sql = "INSERT INTO `users` (`name`,`pwd`,`group`,`status`) VALUES (:name, :pwd, :group, :status)";

		$r = DB::sql($sql, array(
			':name' => trim($name),
			':pwd' => self::encrypt_pwd($pwd),
			':group' => trim($group),
			':status' => trim($status)
			));

		if($r == 1)
			return true;
		else
			return false;
	}

	/**
	 * 修改任意用户密码
	 * @param $uid : 用户在users表里的id  
	 * @param $pwd : 新密码
	 * @return bool 成功返回true 失败返回false
	 */
	static function reset_user_pwd($uid, $pwd)
	{
		$sql = "UPDATE `users` SET `pwd` = :pwd WHERE `id` = :uid";
		$r = DB::sql($sql, array(':uid' => trim($uid), ':pwd' => self::encrypt_pwd($pwd)));

		if($r >= 0)
			return true;
		else
			return false;
	}

	/**
	 * 修改某用户状态
	 * @param $uid : 用户在users表里的id 
	 * @param $status : 用户新状态
	 * @return bool 成功返回true 失败返回false
	 */
	static function change_user_status($uid, $status)
	{
		$sql = "UPDATE `users` SET `status` = :status WHERE `id` = :uid";
		$r = DB::sql($sql, array(':uid' => trim($uid), ':status' => trim($status)));

		if($r >= 0)
			return true;
		else
			return false;

	}

	/**
	 * 根据uid得到用户group
	 * @param $uid : 用户在users表里的id
	 * @return 成功返回group名字  失败返回false
	 */
	private static function get_user_group($uid)
	{
		$sql = "SELECT `group` FROM `users` WHERE `id` = :uid";
		$r = DB::sql($sql , array(':uid' => $uid));

		if(empty($r))
			return false;
		else
			return $r['0']['group'];
	}
		
	private	static function edit_student_info($uid, $data)
	{
		$sql = "UPDATE `student` SET `name` = :name, `no` = :no, `sex` = :sex, `class` = :class, `college` = :college, `major` = :major, `avatar` = :avatar, `email` = :email, `phone` = :phone WHERE `uid` = :uid";
		$r = DB::sql($sql, array( ':uid' => trim($uid), ':name' => trim($data['name']), ':no' => trim($data['no']), ':sex' => trim($data['sex']), ':class' => trim($data['class']), ':college' => trim($data['college']), ':major' => trim($data['major']), ':avatar' => trim($data['avatar']), ':email' => trim($data['email']), ':phone' => trim($data['phone'])));
		if($r >= 0 ) 
			return true;
		else
			return false;

	}

	private static function edit_club_info($uid, $data)
	{
		$sql = "UPDATE `club` SET `name` = :name, `introduction` = :intro WHERE `uid` = :uid";
		$r = DB::sql($sql, array( ':uid' => trim($uid), ':name' => trim($data['name']), ':intro' => trim($data['introduction'])));
		if($r >= 0 ) 
			return true;
		else
			return false;
	}

	private static function edit_org_info($uid, $data)
	{
		$sql = "UPDATE `org` SET `name` = :name, `introduction` = :intro WHERE `uid` = :uid";
		$r = DB::sql($sql, array( ':uid' => trim($uid), ':name' => trim($data['name']), ':intro' => trim($data['introduction'])));
		if($r >= 0 ) 
			return true;
		else
			return false;
	}

	private static function edit_service_info($uid, $data)
	{
		$sql = "UPDATE `service` SET `name` = :name WHERE `uid` = :uid";
		$r = DB::sql($sql, array( ':uid' => trim($uid), ':name' => trim($data['name'])));
		if($r >= 0 ) 
			return true;
		else
			return false;
	}

	/**
	 * 修改某用户基本信息
	 * @param $uid : 用户在users表里的id 
	 * @param $data : 信息关联数组
	 * @return bool 成功返回true  失败返回false
	 */
	static function edit_user_info($uid, $data)
	{
		$group = self::get_user_group($uid);
		switch($group)
		{
			case F3::get('STUDENT_GROUP'):
				return self::edit_student_info($uid, $data);
				break;
			case F3::get('CLUB_GROUP'):
				return self::edit_club_info($uid, $data);
				break;
			case F3::get('ORG_GROUP'):
				return self::edit_org_info($uid, $data);
				break;
			case F3::get('SERVICE_GROUP'):
				return self::edit_service_info($uid, $data);
				break;
			default:
				return false;
		}
	}

};

?>
