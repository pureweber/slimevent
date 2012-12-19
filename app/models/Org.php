<?php

/**
 * 第三方机构类
 * @package Slimevent
 */

class Org extends Account{

	/**
	 * 编辑基本信息
	 * @param $data : 基本信息关联数组
	 * @return bool 成功返回true 失败返回false
	 */
	static function edit_basic_info($data)
	{
		$uid = self::the_user_id();

		$sql = "UPDATE `org` SET `introduction` = :intro WHERE `uid` = :uid";

		$r = DB::sql($sql, array(':intro' => $data['introduction'], ':uid' => $uid));

		if($r >= 0)
			return true;
		else
			return false;
	}

};

?>
