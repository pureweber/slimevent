<?php
/**
 * 学生类
 * @package Slimevent
 */
class Student extends Account{

	/**
	 * 注册学生用户(在users表里插入一条学生记录)
	 * @param $name : 学生学号
	 * @return bool 成功返回true 失败返回false
	 */
	static function register($name)
	{
		$group = F3::get('STUDENT_GROUP');
		$pwd = self::encrypt_pwd(F3::get('DEFAULT_PWD'));
		$status = F3::get('NORMAL_STATUS');

		$sql = "INSERT INTO `users` (`name`,`pwd`,`group`,`status`) VALUES (:name, :pwd, :group, :status)";

		$r = DB::sql($sql, array(
			':name' => trim($name),
			':pwd' => trim($pwd),
			':group' => trim($group),
			':status' => trim($status)
			));

		if($r == 1)
			return true;
		else
			return false;
	}

	/**
	 * 添加个人基本信息
	 * @param $data : 个人信息关联数组
	 * @return bool 成功true  失败false
	 */
	static function add_basic_info($data)
	{
		$uid = self::the_user_id();

		$sql = "INSERT INTO `student` (`uid`,`name`,`no`,`sex`,`class`,`college`,`major`,`avatar`,`email`,`phone`) 
							   VALUES (:uid, :name, :no, :sex, :class, :college, :major, :avatar, :email, :phone)";

		$r = DB::sql($sql, array( ':uid' => trim($uid), ':name' => trim($data['name']), ':no' => trim($data['no']), ':sex' => trim($data['sex']), ':class' => trim($data['class']), ':college' => trim($data['college']), ':major' => trim($data['major']), ':avatar' => trim($data['avatar']), ':email' => trim($data['email']), ':phone' => trim($data['phone'])));

		if($r == 1)
			return true;
		else
			return false;
	}

	/**
	 * 修改个人信息(只能修改avatar email phone)
	 * @param $data : 个人信息关联数组
	 * @return bool 成功true  失败false
	 */
	static function edit_basic_info($data)
	{
		$uid = self::the_user_id();

		$sql = "UPDATE `student` SET `avatar` = :avatar, `email` = :email, `phone` = :phone WHERE `uid` = :uid";

		$r = DB::sql($sql, array( ':uid' => trim($uid), ':avatar' => trim($data['avatar']), ':email' => trim($data['email']), ':phone' => trim($data['phone'])));

		if($r >= 0 ) 
			return true;
		else
			return false;
	}

};

?>
