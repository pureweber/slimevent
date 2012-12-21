<?php
class CAS{

	static function login()
	{
		// phpCAS simple client
		//
		// // import phpCAS lib
		include_once('app/lib/cas/CAS.php');
		phpCAS::setDebug(false);

		// initialize phpCAS
		phpCAS::client(CAS_VERSION_2_0,'cas.hit.edu.cn',443, '');

		$cert = dirname(__FILE__) . '/cas.cer';

		//phpCAS::setCasServerCACert($cert);

		// no SSL validation for the CAS server
		phpCAS::setNoCasServerValidation();

		// force CAS authentication
		phpCAS::forceAuthentication();
		//
		// // at this step, the user has been authenticated by the CAS server
		// // and the user's login name can be read with phpCAS::getUser().
		// for this test, simply print that the authentication was successfull

		$user = trim(phpCAS::getUser());

		if($user == "")
			Sys::error(F3::get("NOT_GET_CAS_ID"));  //没有获得CAS

		return $user;
	}

	static function logout()
	{
		include_once('app/lib/cas/CAS.php');
		phpCAS::setDebug(false);

		// initialize phpCAS
		phpCAS::client(CAS_VERSION_2_0,'cas.hit.edu.cn',443, '');

		$cert = dirname(__FILE__) . '/cas.cer';

		//phpCAS::setCasServerCACert($cert);

		// no SSL validation for the CAS server
		phpCAS::setNoCasServerValidation();

		// force CAS authentication
		phpCAS::forceAuthentication();

		$url = F3::get('WEB_URL').F3::get('WEB_ROOT');

		phpCAS::logout(array('url' => $url));
	}

};
?>
