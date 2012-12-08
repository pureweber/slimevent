<?php
/**
 * Club
 *
 * @package   Slimevent
 **/

class SEClub{

	function __construct(){
		//if(Accounts::check_group("club") === false)
			//F3::reroute("/");
		SECommon::set_unread_msg_num();
	}

<<<<<<< HEAD

=======
	function show_list(){
		F3::set("title", "社团管理");
		$data = array();
		$data[] = array(
			'eid'=>1,
			'title'=>"面向网络社区的问答对识别及语义挖掘研究",
			'date'=>"2012/12/24",
			'join'=>14,
			'praise'=>29
			);
		$data[] = array(
			'eid'=>2,
			'title'=>"2012阿里巴巴“技术梦想，橙就未来”技术沙龙全国巡讲哈尔站",
			'date'=>"2012/12/05",
			'join'=>12,
			'praise'=>20
			);
		F3::set('events', $data);
		echo Template::serve('club/list.html');
	}

	function show_join_list(){
		F3::set("title", "已报名列表");
		$data = array();
		$data[] = array(
			'uid'=>1,
			'date'=>"2012/12/24",
			);
		F3::set('list', $data);
		echo Template::serve('club/join.html');
	}
>>>>>>> 4d1159c62063fc348f4ad35f0b4cf80d93a021da

}


