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

		Account::unset_cookie();
		F3::reroute("/login");
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
};

?>
