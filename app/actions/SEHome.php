<?php
/**
 * SEHomeClass
 *
 * @package   Slimevent
 **/

/**
 * Action for Home
 */

class SEHome{

	function __construct(){
		SECommon::set_unread_msg_num();
	}

	function run()
	{
		echo Template::serve('index.html');
		//if(Account::is_login() === TRUE)
			//echo Template::serve('hello.html');
		//else
			//F3::reroute('/accounts/admin/login');
	}

	function logout()
	{
		Account::logout();
		F3::reroute('/');
	}

};

?>
