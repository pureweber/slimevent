<?php

/**
 * 学生类
 * @package Slimevent
 */

class Student extends Account{

	/**
	 * 注册学生用户(在users表里插入一条学生记录)
	 * @param $name : 学生学号
	 * @return $id 新用户uid
	 */
	static function register($name)
	{
		$data = array(
			'name' => $name,
			'pwd' => self::encrypt_pwd(F3::get('DEFAULT_PWD')),
			'group' => F3::get('STUDENT_GROUP'),
			'status' => F3::get('NORMAL_STATUS')
			);

		EDB::insert('users', $data);

		return DB::get_insert_id();
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

	/**
	 * 当前登录学生报名活动
	 * @param $eid
	 */
	//static function join_event($eid)
	//{
		//$uid = self::the_user_id();
		//JoinList::add($uid, $eid);
	//}

	/**
	 * 当前登录学生取消报名活动
	 * @param $eid
	 */
	//static function unjoin_event($eid)
	//{
		//$uid = self::the_user_id();
		//JoinList::remove($uid, $eid);
	//}

	/**
	 * 当前登录学生赞活动
	 * @param $eid
	 */
	//static function praise_event($eid)
	//{
		//$uid = self::the_user_id();
		//PraiseList::add($uid, $eid);
	//}

	/**
	 * 当前登录学生取消赞活动
	 * @param $eid
	 */
	//static function unpraise_event($eid)
	//{
		//$uid = self::the_user_id();
		//PraiseList::remove($uid, $eid);
	/*}*/
};

?>
