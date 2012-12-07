<?php

/**
 * 用户父类
 * @package slimevent
 */

class Account{

	/**
	 * 设置当前用户cookie
	 * @param $user 用户关联数组
	 */
	private static function set_cookie($user)
	{
		setcookie('se_user_id', $user['id'], time() + 86400, '/');
		setcookie('se_user_name', $user['name'], time() + 86400, '/');
		setcookie('se_user_group', $user['group'], time() + 86400, '/');
		setcookie('se_user_token', self::generate_login_token($user['id'], $user['name'], $user['group']), time() + 86400, '/');
	}

	/**
	 * 清除当前用户cookie
	 */
	private static function unset_cookie()
	{
		setcookie('se_user_id', '', time() - 86400, '/');
		setcookie('se_user_name', '', time() - 86400, '/');
		setcookie('se_user_group', '', time() - 86400, '/');
		setcookie('se_user_token', '', time() - 86400, '/');
	}

	/**
	 * 生成cookie密钥token
	 * @return sring se_user_token
	 */
	private static function generate_login_token($id, $name, $group)
	{
		return md5( $id.$name.$group . F3::get('TOKEN_SALT') );
	}

	/**
	 * 验证cookie是否合法
	 * @return 合法返回true 不合法返回false
	 */
	private static function validate_login_token()
	{
		$id = F3::get('COOKIE.se_user_id');
		$name = F3::get('COOKIE.se_user_id');
		$group = F3::get('COOKIE.se_user_group');
		$token = F3::get('COOKIE.se_user_token');

		$valid = self::generate_login_token($id, $name, $group);

		if($token == $valid)
			return true;
		else
			return false;
	}

	/**
	 * 验证用户名和密码是否正确
	 * @param $name : 用户名  $pwd : 密码
	 * @return 如果正确返回用户关联数组 如果错误返回false
	 */
	private static function valid($name, $pwd)
	{
		$r = DB::sql('SELECT * FROM `users` WHERE `name` = :name AND `pwd` = :pwd', array(
			':name' => $name, ':pwd' => md5($pwd)
		));

		if( count($r) > 0 )
			return $r[0];
		else
			return false;
	}

	/**
	 * 登录系统
	 * @param	$name : 用户名 $pwd : 密码
	 * @return	bool 成功返回true  失败返回false
	 */
	static function login($name, $pwd)
	{
		$name = trim($name);
		$pwd = trim($pwd);

		if(empty($name) || empty($pwd))
			return false;

		$user = self::valid($name, $pwd);

		if($user === false)
			return false;
		else
			self::set_cookie($user);

		return true;
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
		if(self::validate_login_token() === true)
			return F3::get('COOKIE.se_user_id');
		else
			F3::reroute("/error/1");
	}

	/**
	 * 从cookie获取当前用户name
	 * 如果cookie不合法 重定向错误页面
	 * @return 用户name 
	 */
	static function the_user_name()
	{
		if(self::validate_login_token() === true)
			return F3::get('COOKIE.se_user_name');
		else
			F3::reroute("/error/1");
	}

	/**
	 * 从cookie获取当前用户group
	 * 如果cookie不合法 重定向错误页面
	 * @return 用户group 
	 */
	static function the_user_group()
	{
		if(self::validate_login_token() === true)
			return F3::get('COOKIE.se_user_group');
		else
			F3::reroute("/error/1");
	}

	/**
	 * 检测用户名name是否存在
	 * @return 如果存在,返回该用户的关联数组 如果不存在,返回false
	 */
	static function exists($name)
	{
		$sql = 'SELECT * FROM `users` WHERE `name` = :name';
		$r = DB::sql($sql, array(':name' => trim($name)));
		if(empty($r))
		{
			return false;
		}
		else
			return $r[0];
	}

	/**
	 * 插入新用户
	 * @param $name : 用户名 $pwd : 密码 $group : 用户组
 	 * @return bool  成功返回true  失败返回false
	 */
	static function insert($name, $pwd, $group)
	{
		if(trim($name) == '' || trim($pwd) == '' || trim($group) == '')  
			return false;

		$sql = "INSERT INTO `users` (`name`, `pwd`, `group`) VALUES (:name, :pwd, :group)";
		$r = DB::sql($sql, array(
			':name' => trim($name), 
			':pwd' => md5(trim($pwd)),
			':group' => trim($group)
		));

		if($r == 1)
			return true;
		else
			return false;
	}

};

?>
