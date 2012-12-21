<?php

class Sys{

	static function error($error_code, $info = "")
	{
		$COOKIE = LogFile::JSON(F3::get("COOKIE"));
		$ip = F3::get('SERVER.REMOTE_ADDR');
		$user_agent = F3::get('SERVER.HTTP_USER_AGENT');
		$request_uri = F3::get('SERVER.REQUEST_URI');
		$request_method = F3::get('SERVER.REQUEST_METHOD');
		$receive_data = LogFile::JSON(F3::get($request_method));
		$request_time = (string)time(); 
		if(is_array($info))
			$info  = LogFile::JSON($info);

		$data = array(
			'error' => $error_code,
			'cookie' => $COOKIE,
			'info' => $info,
			'ip' => $ip,
			'agent' => $user_agent,
			'url' => $request_uri,
			'method' => $request_method,
			'data' => $receive_data,
			'time' => $request_time
			);

		EDB::insert('errorlog',$data);

		//Account::unset_cookie();
		//F3::reroute("/login");
		F3::reroute("/");
	}

	static function time_quaters(){
		static $times = null;

		if($times == null){
			for($i = 0; $i < 24; $i++){
				for($j = 0; $j < 60; $j += 15){
					$times[] = sprintf('%02d:%02d', $i, $j);
				}
			}
		}

		return $times;
	}

	static function the(){
		$args = func_get_args();
		$arr = $args[0];

		if($args < 2 || !is_array($arr)){
			return '';
		}


		for($i = 1; $i < count($args); $i++){
			$k = $args[$i];

			if(is_array($arr) && array_key_exists($k, $arr)){
				$arr = $arr[$k];
			}else{
				break;
			}
		}

		return $arr;
	}

	static function resize_image($image_path, $max_width, $max_height)
{
	list($width, $height, $type) = getimagesize($image_path);

	$types = array(
		1 => 'imagecreatefromgif',
		2 => 'imagecreatefromjpeg',
		3 => 'imagecreatefrompng',
		6 => 'imagecreatefromwbmp');

	$creater = $types[$type];

	if(!function_exists($creater))
		return -1;

	$img = $creater($image_path);
	$dist_img = $img;

	$ratio = $width / $height;

	if($ratio > $max_width/$max_height){
		if($width > $max_width)
		{
			$d_height = intval($max_width / $ratio);
			$dist_img = imagecreatetruecolor($max_width, $d_height);
			imagecopyresampled($dist_img, $img,
				0, 0,	0, 0,
				$max_height, $d_height,	$width, $height);

		}
	}
	else{
		if($height > $max_height)
		{
			$d_width = intval($max_height * $ratio);
			$dist_img = imagecreatetruecolor($d_width, $max_height);
			imagecopyresampled($dist_img, $img,
				0, 0,	0, 0,
				$d_width, $max_height,	$width, $height);
		}
	}

	return $dist_img;
}

};

?>
