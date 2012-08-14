<?php
session_start();

include('app/lib/google-api/apiClient.php');
include('app/lib/google-api/contrib/apiCalendarService.php');

$apiClient = new apiClient();

$apiClient->setUseObjects(true);

$service = new apiCalendarService($apiClient);

if (isset($_SESSION['oauth_access_token'])) {
  $apiClient->setAccessToken($_SESSION['oauth_access_token']);
} else {
  $token = $apiClient->authenticate();
  $_SESSION['oauth_access_token'] = $token;
}

$calendarList = $service->calendarList->listCalendarList();

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
				<h1>欢迎你</h1>
				<div class="btn-group">
					<a class="btn btn-large" href="cas.php?logout"><i class="icon-user"></i> 退出登陆</a>
					<a class="btn btn-large btn-success" href="#"><i class="icon-plus-sign"></i> 发起一个事件</a>
				</div>

<ul>
<?php
while(true) {
	foreach ($calendarList->getItems() as $calendarListEntry) {
		echo '<li>', $calendarListEntry->getSummary(), '</li>';
	}
	$pageToken = $calendarList->getNextPageToken();
	if ($pageToken) {
		$optParams = array('pageToken' => $pageToken);
		$calendarList = $service->calendarList->listCalendarList($optParams);
	} else {
		break;
	}
}

?>
</ul>

			</article>

			<footer id="foot">
			<p style="text-align:center;">Copyright &copy; PureWeber.com</p>
			</footer>
		</div><!-- Container -->
	</div>
</body>
</html>
