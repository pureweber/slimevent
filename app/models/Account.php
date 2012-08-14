<?php

class Account{

	static function exists($email){
		if(trim($email) == ''){
			return 1;
		}
		$r = DB::sql('SELECT COUNT(*) AS num FROM `users` WHERE `email` = :email', array(':email' => $email));

		return $r[0][num];
	}

	static function valid($email, $pass){
		$email = trim($email);
		$pass = trim($pass);

		if(empty($email) || empty($pass)){
			return false;
		}

		$r = DB::sql('SELECT * FROM `users` WHERE `email` = :email AND `password` = :password', array(
			':email' => $email, ':password' => md5($pass)
		));

		if( count($r) > 0 ){
			return $r[0];
		}else{
			return false;
		}
	}

	static function login($user){
		setcookie('se_user_id', $user['id']);
		setcookie('se_user_name', $user['displayname']);
		setcookie('se_user_token', self::generate_login_token($user['id']));
	}

	static function logout(){
		setcookie('se_user_id', '', time() - 86400);
		setcookie('se_user_name',  '', time() - 86400);
		setcookie('se_user_token',  '', time() - 86400);
	}

	static function is_login(){
		$cookie = F3::get('COOKIE');

		if($cookie['se_user_id'] != ''){
			return self::validate_login_token($cookie['se_user_id'], $cookie['se_user_token']);
		}else{
			return false;
		}
	}

	static function the_user_id(){
		return F3::get('COOKIE.se_user_id');
	}
	static function the_user_name(){
		return F3::get('COOKIE.se_user_name');
	}

	static function generate_login_token($uid){
		return md5( $uid . F3::get('TOKEN_SALT') );
	}

	static function validate_login_token($uid, $token){
		$valid = self::generate_login_token($uid);

		return $token == $valid;
	}
};

?>
