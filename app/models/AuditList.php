<?php

	/**
	 * 审核记录表
	 * @package Slimevent
	 */

class AuditList{
	/*
	 * 插入一条活动审核记录
	 * @param $eid int
	 * @param $uid int 
	 * @param $result enum
	 * @param $comments string
	 */
	static function create($eid, $uid, $result, $comments="")
	{
		$data = array(
			'eid' => $eid,
			'uid' => $uid,
			'result' => $result,
			'comments' => $comments,
			'time' => time()
		);

		if(EDB::counts('audit','eid',$eid) == 0)
			EDB::insert('audit', $data);
		else
		{
			unset($data['eid']);
			EDB::update('audit', $data,'eid', $eid);
		}
	}

};

?>
