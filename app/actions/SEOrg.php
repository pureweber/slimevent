
<?php
/**
 * SEHomeClass
 *
 * @package   Slimevent
 **/

/**
 * Action for Home
 */

class SEOrg extends SECommon{

	function __construct(){
		if(Account::the_user_group() != F3::get("ORG_GROUP"))
			Sys::error(F3::get("INVALID_GROUP_CODE"), Account::the_user_id());

		$this->set_unread_msg_num();
	}

}
