<?php

/**
 * 用户父类
 * @package Slimevent
 */

class Account{

	/**
	 * 设置当前用户cookie
	 * @param $user 用户关联数组
	 */
	protected static function set_cookie($user)
	{
		setcookie('se_user_id', $user['id'], time() + F3::get('COOKIE_TIME'), '/');
		setcookie('se_user_name', $user['name'], time() + F3::get('COOKIE_TIME'), '/');
		setcookie('se_user_group', $user['group'], time() + F3::get('COOKIE_TIME'), '/');
		setcookie('se_user_token', self::generate_login_token($user), time() + F3::get('COOKIE_TIME'), '/');
	}

	/**
	 * 清除当前用户cookie
	 */
	protected static function unset_cookie()
	{
		setcookie('se_user_id', '', time() - F3::get('COOKIE_TIME'), '/');
		setcookie('se_user_name', '', time() - F3::get('COOKIE_TIME'), '/');
		setcookie('se_user_group', '', time() - F3::get('COOKIE_TIME'), '/');
		setcookie('se_user_token', '', time() - F3::get('COOKIE_TIME'), '/');
	}

	/**
	 * 生成cookie密钥token
	 * @return sring se_user_token
	 */
	protected static function generate_login_token($user)
	{
		return md5( $user['id'].$user['name'].$user['group'] . F3::get('TOKEN_SALT') );
	}

	/**
	 * 验证cookie是否合法 如果不合法跳转错误页面
	 */
	protected static function validate_login_token()
	{
		$user = array(
			'id' => F3::get('COOKIE.se_user_id'),
			'name' => F3::get('COOKIE.se_user_name'),
			'group' => F3::get('COOKIE.se_user_group')
			);
		$token = F3::get('COOKIE.se_user_token');
		$valid = self::generate_login_token($user);

		if($token != $valid)
		{
			self::unset_cookie();
			Sys::error(F3::get('COOKIE_ILLEGAL_CODE'));
		}
	}

	/**
	 * 验证用户名和密码是否正确
	 * @param $name : 用户名  $pwd : 密码
	 * @return 如果正确返回用户关联数组 如果错误返回false
	 */
	protected static function valid($name, $pwd)
	{
		$r = DB::sql('SELECT * FROM `users` WHERE `name` = :name AND `pwd` = :pwd', array(
			':name' => trim($name), ':pwd' => self::encrypt_pwd($pwd)
		));

		if( count($r) == 0 )
			return false;
		else if( count($r) == 1 )
			return $r[0];
		else
			Sys::error(F3::get('USERS_NAME_SAME_CODE'), $name);
	}

	/**
	 * 加密密码
	 * @param $pwd
	 * @return 加密后密码
	 */
	protected static function encrypt_pwd($pwd)
	{
		return md5(F3::get('PWD_SALT').trim($pwd));
	}

	/**
	 * 登录系统
	 * @param  $name : 用户名 
	 * @param  $pwd : 密码
	 * @return true : 正确登录	false : 用户名或密码不正确  
	 */
	static function login($name, $pwd)
	{
		$user = self::valid($name, $pwd);

		if($user === false)
			return false;	//用户名或密码不正确
		elseif($user['status'] == F3::get('NORMAL_STATUS'))
		{
			self::set_cookie($user);
			return true;	//正确登录
		}
		elseif($user['status'] == F3::get('BLACK_STATUS'))
			Sys::error(F3::get('BLACK_USER_CODE'), $user['name']);  //黑名单
		elseif($user['status'] == F3::get('DELETED_STATUS'))
			Sys::error(F3::get('DELETE_USER_CODE'), $user['name']);  //已删除用户
		else
			Sys::error(F3::get('INVALID_USER_CODE'), $user['name']); //无效用户状态
	}

	/**
	 * 退出系统
	 */
	static function logout()
	{
		self::unset_cookie();
	}

	/**
	 * 从cookie获取当前用户id
	 * 如果cookie不合法 重定向错误页面
	 * @return 用户id 
	 */
	static function the_user_id()
	{
		self::validate_login_token();
		return F3::get('COOKIE.se_user_id');
	}

	/**
	 * 从cookie获取当前用户name
	 * 如果cookie不合法 重定向错误页面
	 * @return 用户name 
	 */
	static function the_user_name()
	{
		self::validate_login_token();
		return F3::get('COOKIE.se_user_name');
	}

	/**
	 * 从cookie获取当前用户group
	 * 如果cookie不合法 重定向错误页面
	 * @return 用户group 
	 */
	static function the_user_group()
	{
		self::validate_login_token();
		return F3::get('COOKIE.se_user_group');
	}

	/**
	 * 检测用户名name是否存在
	 * @return 如果存在,返回该用户的关联数组 如果不存在,返回false
	 */
	static function exists($name)
	{
		$sql = 'SELECT * FROM `users` WHERE `name` = :name';
		$r = DB::sql($sql, array(':name' => trim($name)));

		if( count($r) == 0 )
			return false;
		else if( count($r) == 1 )
			return $r[0];
		else
			Sys::error(F3::get('USERS_NAME_SAME_CODE'), $name);
	}

	/**
	 * 编辑基本信息 虚构函数
	 * @param $info : 基本信息关联数组
	 * @return bool 修改成功返回true  失败返回false
	 */
	static function edit_basic_info($info)
	{

	}

	/**
	 * 在event表里创建一个新活动
	 * @param $data : 活动信息关联数组
	 * @return $eid : 新建活动的id
	 */
	static function create_event($data)
	{
		return Event::create($data);
	}

	private static function verify_edit_event_permission($eid)
	{
		if(self::the_user_group() == F3::get('ADMIN_GROUP'))
			return true;
//		if(self::the_user_group() == F3::get('SERVICE_GROUP'))
//			return true;

		$e = Event::show($eid);
		if(self::the_user_id() == $e['organizer'])
			return true;

		return false;
	}

	/**
	 * 根据eid更新活动信息
	 * @param $eid
	 * @param $data 需要更新信息的关联数组(请不要包含eid)
	 * @return true : 更新成功 false : 没有更新
	 */
	static function edit_event($eid, $data)
	{
		if(self::verify_edit_event_permission($eid) === false)
			Sys::error(F3::get('ILLEGAL_EDIT_EVENT_CODE'), $eid);
		else
			return Event::update($eid, $data);
	}

	private static function verify_del_event_permission($eid)
	{
		if(self::the_user_group() == F3::get('ADMIN_GROUP'))
			return true;
		if(self::the_user_group() == F3::get('SERVICE_GROUP'))
			return true;

		$e = Event::show($eid);
		if(self::the_user_id() == $e['organizer'])
			return true;

		return false;
	}

	/**
	 * 删除id为eid的活动信息
	 * @param $eid
	 */
	static function delete_event($eid)
	{
		if(self::verify_del_event_permission($eid) === false)
			Sys::error(F3::get('ILLEGAL_DELELE_EVENT_CODE'), $eid);
		else
		{
			$data = array('status' => F3::get('EVENT_DELETED_STATUS') );
			Event::update($eid, $data);
		}
	}

};

?>
