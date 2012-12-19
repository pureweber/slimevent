<?php

date_default_timezone_set('Asia/Harbin');

require __DIR__.'/app/lib/base.php';

F3::config('app/cfg/setup.cfg');
F3::config('app/cfg/constant.cfg');
F3::config('app/cfg/index.cfg');
F3::config('app/cfg/errno.cfg');
F3::config('app/cfg/routes.cfg');

F3::set('DB', new DB(
//	'mysql:host=192.168.17.254;port=3306;dbname=slimevent',
	'mysql:host=localhost;port=3306;dbname=slimevent',
	'root',
	//'123'
	'vpcm'
));

F3::run();

?>
