<?php
/**
 * 社团类
 * @package Slimevent
 */
class Club extends Account{

	/**
	 * 编辑基本信息
	 * @param $info : 基本信息关联数组
	 * @return bool 成功返回true 失败返回false
	 */
	static function edit_basic_info($info)
	{
		$uid = self::the_user_id();
		$sql = "UPDATE `club` SET `introduction` = :intro WHERE `uid` = :uid";

		$r = DB::sql($sql, array(':intro' => $info['introduction'], ':uid' => $uid));

		if($r >= 0)
			return true;
		else
			return false;
	}

};

?>
