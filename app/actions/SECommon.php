<?php
/**
 * Common
 *
 * @package   Slimevent
 **/

class SECommon{

	static function set_unread_msg_num(){
		//$unread_msg_num = MsgBox::get_unread_num();
		F3::set("unread_msg", 4);
	}

}
