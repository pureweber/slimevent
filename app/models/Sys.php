<?php

class Sys{
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
