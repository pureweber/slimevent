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
	static function set_cookie($user)
	{
		setcookie('se_user_id', $user['id'], time() + F3::get('COOKIE_TIME'), '/');
		setcookie('se_user_name', $user['nickname'], time() + F3::get('COOKIE_TIME'), '/');
		setcookie('se_user_group', $user['group'], time() + F3::get('COOKIE_TIME'), '/');
		setcookie('se_user_token', self::generate_login_token($user), time() + F3::get('COOKIE_TIME'), '/');
		setcookie('se_theme', $user['theme'], time() + F3::get('COOKIE_TIME'), '/');
	}

	/**
	 * 清除当前用户cookie
	 */
	static function unset_cookie()
	{
		setcookie('se_user_id', '', time() - F3::get('COOKIE_TIME'), '/');
		setcookie('se_user_name', '', time() - F3::get('COOKIE_TIME'), '/');
		setcookie('se_user_group', '', time() - F3::get('COOKIE_TIME'), '/');
		setcookie('se_user_token', '', time() - F3::get('COOKIE_TIME'), '/');
		
		setcookie('se_theme', '', time() - F3::get('COOKIE_TIME'), '/');
	}

	static function update_cookie()
	{
		$uid = Account::the_user_id();
		self::unset_cookie();
		$user = Account::get_user($uid);
		self::set_cookie($user);
	}
	/**
	 * 生成cookie密钥token
	 * @return sring se_user_token
	 */
	protected static function generate_login_token($user)
	{
		return md5( $user['id'].$user['nickname'].$user['group'] . F3::get('TOKEN_SALT') );
	}

	/**
	 * 验证cookie是否合法 如果不合法跳转错误页面
	 */
	protected static function validate_login_token()
	{
		$user = array(
			'id' => F3::get('COOKIE.se_user_id'),
			'nickname' => F3::get('COOKIE.se_user_name'),
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
	static function encrypt_pwd($pwd)
	{
		return md5(F3::get('PWD_SALT').$pwd);
	}

	static function is_login()
	{
		$uid = F3::get('COOKIE.se_user_id');

		if($uid === null)  //没有登录
			return false;
		else if($uid == self::the_user_id()) //合法登录
			return $uid;
	}

	static function view_one_event($eid)
	{
		$e = Event::show($eid, false);
		$uid = self::is_login();

		if($uid === false)  //未登录用户查阅
		{
			if($e['status'] == F3::get('EVENT_PASSED_STATUS'))	//未登录只能查阅审核通过状态
				return $e;
		}
		else  //登录用户
		{
			$sta = $e['status'];
			switch (self::the_user_group())
			{
				case F3::get('ADMIN_GROUP'):  //我是管理员 什么都可以看
					return $e;	
				case F3::get('SERVICE_GROUP'):  //我是客服 只能看 待审 通过 未通过
					if($sta == F3::get('EVENT_AUDIT_STATUS') || $sta == F3::get('EVENT_FAILED_STATUS') || $sta == F3::get('EVENT_PASSED_STATUS'))
						return $e;
				default:  //学生 社团 机构  (黑名单根本无法登录)  可以查阅别人的通过的活动 以及 自己的未删除的活动
					if($sta == F3::get('EVENT_PASSED_STATUS') || ($sta !== F3::get('EVENT_DELETED_STATUS') && $uid == $e['organizer_id']))
						return $e;
			}
		}

		Sys::error(F3::get('EVENT_NOT_EXIST_CODE', $eid));
	}

	/**
	 * 根据$uid 和 $group得到用户的基本个人信息
	 * @return array 用户信息关联数组
	 */
	protected static function get_user_basic_info($uid, $group)
	{
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
			case F3::get('ADMIN_GROUP'):
				$table = 'admin';
				break;
			default:
				Sys::error(F3::get('INVALID_GROUP_CODE'), $uid);
		}
		$b = EDB::select($table, 'uid', $uid);

		if( count($b) == 0 )
			Sys::error(F3::get('NOT_EXIST_USER_INFO_CODE'), $uid);
		elseif( count($b) > 1 )
			Sys::error(F3::get('USERS_INFO_ID_SAME_CODE'), $uid);

		return $b[0];
	}

	/**
	 * 得到$uid用户的所有信息
	 * @return 二维数组 [0]存的是users表里的关联信息 [1]存的是关联的基本信息
	 */
	static function get_user_full_info($uid)
	{
		$u = EDB::select('users', 'id', $uid);

		if( count($u) == 0 )
			Sys::error(F3::get('NOT_EXIST_USER_CODE'), $uid);
		elseif( count($u) > 1 )
			Sys::error(F3::get('USERS_ID_SAME_CODE'), $uid);

		$u[1] = self::get_user_basic_info($uid, $u[0]['group']);
		return $u;
	}

	static function get_user($uid)
	{
		$u = EDB::select('users', 'id', $uid);

		if( count($u) == 0 )
			Sys::error(F3::get('NOT_EXIST_USER_CODE'), $uid);
		elseif( count($u) > 1 )
			Sys::error(F3::get('USERS_ID_SAME_CODE'), $uid);

		return $u[0];
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
			self::set_cookie($user); 		//设置cookie
			//更新最后一次登录时间
			EDB::update('users', array('last_time' => time()), 'id', $user['id']);
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
	 * 检测用户名为name 或者昵称为nickname的用户(排除uid用户）是否存在
	 * @return 如果存在 array 如果不存在,返回false
	 */
	static function exists($name = "", $nickname = "", $uid = -1)
	{
		$sql = "SELECT * FROM `users` WHERE (`name` = :name OR `nickname` = :nickname) AND `id` != :uid";
		$r = DB::sql($sql, array(':name' => trim($name), ':nickname' => trim($nickname), ':uid' => trim($uid)));

		if( count($r) == 0 )
			return false;
		else
			return $r[0];
	}

	/**
	 * 更新用户uid的昵称为nickname
	 * @return true 更新成功 false 更新失败 昵称重复或者昵称为空
	 */
	static function update_user_nickname($uid, $nickname)
	{
		if(trim($nickname) == "" || (self::exists("", $nickname, $uid) !== false))
			return false;

		$sql = "UPDATE `users` SET `nickname` = :nickname WHERE `id` = :uid";
		DB::sql($sql, array(':nickname' => trim($nickname), ':uid' => $uid));
		return true;
	}

	/**
	 * 更新用户uid的个人信息
	 * @param $info : 信息关联数组
	 * @return true 更新成功 false 更新失败 昵称重复或者昵称为空
	 */
	static function update_user_info($uid, $info)
	{
		$u = self::get_user($uid);
		$group = $u['group'];

		if(self::update_user_nickname($uid, $info['nickname']) === false)
			return false;

		unset($info['nickname']);
		EDB::update($group, $info, 'uid', $uid);
		return true;
	}

	/**
	 * 在event表里创建一个新活动
	 * @param $data : 活动信息关联数组
	 * @return $eid : 新建活动的id
	 */
	static function create_event($data)
	{
		$data['organizer_id'] = self::the_user_id();
		$data['old_id'] = F3::get('NO_OLD_ID');		//首次创建活动无老板本id
		$data['post_time'] = time();
		$data['status'] = F3::get('EVENT_DRAFT_STATUS');   //默认创建活动是草稿状态

		return Event::create($data);
	}

	/**
	 * 验证当前用户能否查看eid活动
	 * @param $eid
	 * @return bool 
	 */
	static function preview_event($eid)
	{
		$e = Event::get_basic_info($eid);

		//该活动是我的 且未删除
		//Code::dump($e);
		//Code::dump(self::the_user_id());
		//Code::dump($e);
		

		if(self::the_user_id() == $e['organizer_id'] && $e['status'] != F3::get('EVENT_DELETED_STATUS'))
			return true;
		else
			return false;
	}

	/**
	 * 验证当前用户是否有发布 编辑 删除 查看活动报名名单$eid活动信息的权利
	 * @param $eid
	 * @return 有权力返回 活动基本信息关联数组array  没有权力 false
	 */
	static function verify_handle_event_permission($eid)
	{
		$e = Event::get_basic_info($eid);

		if(self::the_user_group() == F3::get('ADMIN_GROUP'))   //我是管理员
			return $e;

//		if(self::the_user_group() == F3::get('SERVICE_GROUP'))  //我是客服
//			return $e;

		if(self::the_user_id() == $e['organizer_id']) 		//该活动是我的
			return $e;

		return false;
	}

	/**
	 * 发布一条活动
	 * 将草稿状态的$eid活动转换为发布状态(等待审核)
	 * @param $eid 活动id
	 * @return $eid
	 */
	static function publish_event($eid)
	{
		$e = self::verify_handle_event_permission($eid);

		if($e === false)
			Sys::error(F3::get('ILLEGAL_PUBLISH_EVENT_CODE'));  //无权操作

		if($e['status'] == F3::get('EVENT_DRAFT_STATUS'))	 //必须草稿状态
		{
			$data['status'] = F3::get('EVENT_AUDIT_STATUS');
			Event::update($eid, $data);
		}
		return $eid;
	}

	/**
	 * 根据eid修改活动信息
	 * @param $eid
	 * @param $data 需要修改信息的关联数组(请不要包含eid)
	 * @return 更新后的eid
	 */
	static function edit_event($eid, $data)
	{
		$e = self::verify_handle_event_permission($eid);

		if($e === false)   
			Sys::error(F3::get('ILLEGAL_EDIT_EVENT_CODE'), $eid);  //无权操作

		switch($e['status'])
		{
			case F3::get('EVENT_DRAFT_STATUS'):
				Event::update($eid, $data);
				return $eid;
			case F3::get('EVENT_AUDIT_STATUS'):
				Event::update($eid, $data);
				return $eid;
			case F3::get('EVENT_PASSED_STATUS'):
				$old_id = Event::get_backup($eid); //之前已经备份的版本
				$new_eid = Event::backup($e); 	//备份一下活动
				if(Event::update($new_eid, $data) === true)  //对新备份的作了内容修改
				{
					if($old_id !== false)  //之前已经有老的版本
						Event::deleted($old_id);  //删除老的备份版本
					Event::update($new_eid, array('status' => F3::get('EVENT_AUDIT_STATUS')));  //把新备份的状态变为等待审核
					return $new_eid;
				}
				else
				{
					Event::deleted($new_eid);  //没做任何修改 把之前备份的删除
					return $eid;
				}
			case F3::get('EVENT_FAILED_STATUS'):
				if(Event::update($eid, $data) === true)  //作了内容修改
					Event::update($eid, array('status' => F3::get('EVENT_AUDIT_STATUS')));  //状态变为等待审核
				return $eid;
			default:
				Sys::error(F3::get('EVENT_HAVE_DELETED_CODE'), $eid);  //活动已被删除
		}
	}

	/**
	 * 删除id为eid的活动信息 和它关联的子活动
	 * @param $eid
	 */
	static function delete_event($eid)
	{
		$e = self::verify_handle_event_permission($eid);

		if($e === false)
			Sys::error(F3::get('ILLEGAL_DELELE_EVENT_CODE'), $eid);
		else
		{
			$old_id = Event::get_backup($eid); //之前已经备份的版本
			if($old_id !== false)
				Event::deleted($old_id);		//删除它的儿子版本
			Event::update($eid, array('status' => F3::get('EVENT_DELETED_STATUS')));
		}
	}

};

?>
