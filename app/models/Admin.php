<?php

/**
 * 管理员类
 * @package Slimevent
 */

class Admin	extends Account{

	/*
	 * 新建一个用户
	 * @param $user : 用户信息关联数组
	 * @return bool 成功返回true  失败返回false
	 */
	static function add_user($name, $pwd, $group)
	{
		$sql = "INSERT INTO `users` (`name`,`pwd`,`group`) VALUES (:name, :pwd, :group)";

		$r = DB::sql($sql, array(
			':name' => trim($name),
			':pwd' => self::encrypt_pwd($pwd),
			':group' => trim($group)
			));

		if($r == 1)
			return true;
		else
			return false;
	}

	/**
	 * 修改任意用户密码
	 * @param $uid : 用户在users表里的id  $pwd : 新密码
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
	 * 修改任意用户group
	 * @param $uid : 用户在users表里的id  $group : 新的group
	 * @return bool 成功返回true 失败返回false
	 */
	static function change_user_group($uid, $group)
	{
			//等待实现
	}

	/**
	 * 根据uid得到用户group
	 * @param $uid : 用户在users表里的id
	 * @return 成功返回group名字  失败返回false
	 */
	static function get_user_group($uid)
	{
		$sql = "SELECT `group` FROM `users` WHERE `id` = :uid";
		$r = DB::sql($sql , array(':uid' => $uid));

		if(empty($r))
			return false;
		else
			return $r['0']['group'];
	}

	/**
	 * 编辑任意用户基本信息
	 * @param $uid : 用户在users表里的id $group : 用户所属组 $data : 信息关联数组
	 * @return bool 成功返回true  失败返回false
	 */
	static function edit_user_info($uid, $group, $data)
	{
		 switch($group)
		 {
			case F3::get('STUDENT_GROUP'):

				$sql = "UPDATE `student` SET `name` = :name, `no` = :no, `sex` = :sex, `class` = :class, `college` = :college, `major` = :major, `avatar` = :avatar, `email` = :email, `phone` = :phone WHERE `uid` = :uid";
				$r = DB::sql($sql, array( ':uid' => trim($uid), ':name' => trim($data['name']), ':no' => trim($data['no']), ':sex' => trim($data['sex']), ':class' => trim($data['class']), ':college' => trim($data['college']), ':major' => trim($data['major']), ':avatar' => trim($data['avatar']), ':email' => trim($data['email']), ':phone' => trim($data['phone'])));
				break;

			case F3::get('CLUB_GROUP'):

				$sql = "UPDATE `club` SET `name` = :name, `introduction` = :intro WHERE `uid` = :uid";
				$r = DB::sql($sql, array( ':uid' => trim($uid), ':name' => trim($data['name']), ':intro' => trim($data['introduction'])));
				break;

			default:
				return false;
		 }

		if($r >= 0)
			return true;
		else
			return false;
	}

};

?>
