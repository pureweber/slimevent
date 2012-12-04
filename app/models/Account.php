<?php

class Account{

	/*
		根据uid和group修改用户名字
		并且返回更新后的用户关联数组
	*/
	static function update($user){

		$sql = "UPDATE `snsUsers` SET `name` = :name WHERE `uid` = :uid AND `group` = :group";
		DB::sql($sql, array(
			':name' => trim($user['name']), 
			':uid' => trim($user['uid']),
			':group' => trim($user['group']) 
			));

		return self::exists($user);
	}

	/*
		检测name用户是否存在
		如果存在,返回该用户的关联数组
		如果不存在,返回 FALSE
	*/
	static function exists($name)
	{
		$sql = 'SELECT * FROM `users` WHERE `name` = :name';
		$r = DB::sql($sql, array(':name' => trim($name)));

		if(empty($r))
			return FALSE;
		else
			return $r[0];
	}

	/*
		插入一个新用户的uid和grouph和name
		如果插入成功,返回新用户的关联数组
		如果插入失败,返回 FALSE
	*/
	static function insert($name, $pwd, $group){

		if(trim($name) == '' || trim($pwd) == '' || trim($group) == '')  
			return FALSE;

		$sql = "INSERT INTO `users` (`name`, `pwd`, `group`) VALUES (:name, :pwd, :goup)";
		$r = DB::sql($sql, array(
			':name' => trim($name), 
			':pwd' => md5(trim($pwd)),
			':group' => trim($group)
			));

		if($r == 1)
			return self::exists($user);
		else
			return FALSE;
	}

	static function valid($name, $pwd){
		$name = trim($name);
		$pwd = trim($pwd);

		if(empty($name) || empty($pwd)){
			return false;
		}

		$r = DB::sql('SELECT * FROM `users` WHERE `name` = :name AND `pwd` = :pwd', array(
			':name' => $name, ':pwd' => md5($pwd)
		));

		if( count($r) > 0 ){
			return $r[0];
		}else{
			return false;
		}
	}

	static function set_cookie($user)
	{
		setcookie('se_user_id', $user['id'], time() + 86400, '/');
		setcookie('se_user_name', $user['name'], time() + 86400, '/');
		setcookie('se_user_group', $user['group'], time() + 86400, '/');
		setcookie('se_user_token', self::generate_login_token($user['id']), time() + 86400, '/');
		setcookie('se_login', "true", time() + 86400, '/');
	}

	static function unset_cookie()
	{
		setcookie('se_user_id', '', time() - 86400, '/');
		setcookie('se_user_name', '', time() - 86400, '/');
		setcookie('se_user_group', '', time() - 86400, '/');
		setcookie('se_user_token', '', time() - 86400, '/');
		setcookie('se_login', '', time() - 86400, '/');
	}

	static function is_login(){
		$cookie = F3::get('COOKIE');

		if(!isset($cookie['se_user_id']) || !isset($cookie['se_user_token']))
			return FALSE;

		if($cookie['se_user_id'] != ''){
			return self::validate_login_token($cookie['se_user_id'], $cookie['se_user_token']);
		}else{
			return FALSE;
		}
	}

	static function the_user_id(){
		return F3::get('COOKIE.se_user_id');
	}
	static function the_user_name(){
		return F3::get('COOKIE.se_user_name');
	}

	static function generate_login_token($id){
		return md5( $id . F3::get('TOKEN_SALT') );
	}

	static function validate_login_token($id, $token){
		$valid = self::generate_login_token($id);

		if($token == $valid)
			return TRUE;
		else
			return FALSE;
	}
};

?>
