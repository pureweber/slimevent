<?php
/**
 * 社团类
 * @package Slimevent
 */
class Club extends Account{

	/**
	 * 编辑基本信息
	 * @param $data : 基本信息关联数组
	 */
	static function edit_basic_info($data)
	{
		$uid = self::the_user_id();
		self::get_user($uid);
		EDB::update('club', $data, 'uid', $uid);
	}

};

?>
