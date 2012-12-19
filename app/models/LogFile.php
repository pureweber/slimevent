<?php

	/**
	 * 日志类  用来分析用户数据 为用户提供个性化服务
	 * @package Slimevent
	 */

class LogFile{

		/**************************************************************
		 *
		 *	使用特定function对数组中所有元素做处理
		 *	@param	string	&$array		要处理的字符串
		 *	@param	string	$function	要执行的函数
		 *	@return boolean	$apply_to_keys_also		是否也应用到key上
		 *	@access public
		 *
		 *************************************************************/
		static function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
		{
			static $recursive_counter = 0;
			if (++$recursive_counter > 1000) {
				die('possible deep recursion attack');
			}
			foreach ($array as $key => $value) {
				if (is_array($value)) {
					self::arrayRecursive($array[$key], $function, $apply_to_keys_also);
				} else {
					$array[$key] = $function($value);
				}
		 
				if ($apply_to_keys_also && is_string($key)) {
					$new_key = $function($key);
					if ($new_key != $key) {
						$array[$new_key] = $array[$key];
						unset($array[$key]);
					}
				}
			}
			$recursive_counter--;
		}
		 
		/**************************************************************
		 *
		 *	将数组转换为JSON字符串（兼容中文）
		 *	@param	array	$array		要转换的数组
		 *	@return string		转换得到的json字符串
		 *	@access public
		 *
		 *************************************************************/
		static function JSON($array) {
			self::arrayRecursive($array, 'urlencode', true);
			$json = json_encode($array);
			return urldecode($json);
		}

	/**
	 * 插入一条用户访问网站的相关信息记录
	 */
	static function add()
	{
		$uid = (string)Account::is_login();
		$ip = F3::get('SERVER.REMOTE_ADDR');
		$user_agent = F3::get('SERVER.HTTP_USER_AGENT');
		$request_uri = F3::get('SERVER.REQUEST_URI');
		$request_method = F3::get('SERVER.REQUEST_METHOD');
		$receive_data = self::JSON(F3::get($request_method));
		$request_time = (string)time(); 

		$data = array(
			'uid' => $uid,
			'ip' => $ip,
			'agent' => $user_agent,
			'url' => $request_uri,
			'method' => $request_method,
			'data' => $receive_data,
			'time' => $request_time
			);
//		echo "日志已经记录";
//		Code::dump($data);
		EDB::insert('logfile', $data);
	}
};

?>
