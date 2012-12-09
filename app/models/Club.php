<?php
/**
 * 社团类
 * @package Slimevent
 */
class Club extends Account{

	/**
	 * 编辑基本信息
	 * @param $data : 基本信息关联数组
	 * @return bool 成功返回true 失败返回false
	 */
	static function edit_basic_info($data)
	{
		$uid = self::the_user_id();

		$sql = "UPDATE `club` SET `introduction` = :intro WHERE `uid` = :uid";

		$r = DB::sql($sql, array(':intro' => $data['introduction'], ':uid' => $uid));

		if($r >= 0)
			return true;
		else
			return false;
	}
	
	static function create_event($data)
	{
		$data['verify'] = F3::get('EVENT_PASSED_VERIFY');
		$r = Event::create($data);
	}

	static function update_event($eid)
	{
		$r = Event::update($data);
	}

	private static function verify_permission($eid)
	{
		$group = Account::the_user_group();

		if($group == F3::get('ADMIN_GROUP')
			return true;
		else
			$sql	
			$uid = Account::the_user_id();


	}

};

?>
