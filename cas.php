<?php
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
// // logout if desired
if (isset($_REQUEST['logout'])) {
	phpCAS::logout();
}
// for this test, simply print that the authentication was successfull
?>
<!DOCTYPE html>
<html>
<head> 
	<meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
	<title>Event@HIT</title>
	<link type="text/css" rel="stylesheet" href="static/bootstrap/css/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="static/css/font-awesome.css" />
</head>
<body style="padding-top:70px;">
	<div id="wrap">
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="brand" href="http://event.pureweber.com">工大事件</a>
					<ul class="nav">
						<li><a href="http://event.pureweber.com">首页</a></li>
						<li><a href="#">发现</a></li>
						<li><a href="#">我的事件</a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="container">
			<article class="event">
				<h1>欢迎你, <?php echo phpCAS::getUser();?>!</h1>
				<div class="btn-group">
					<a class="btn btn-large" href="cas.php?logout"><i class="icon-user"></i> 退出登陆</a>
					<a class="btn btn-large btn-success" href="#"><i class="icon-plus-sign"></i> 发起一个事件</a>
				</div>
			</article>

			<footer id="foot">
			<p style="text-align:center;">Copyright &copy; PureWeber.com</p>
			</footer>
		</div><!-- Container -->
	</div>
</body>
</html>
