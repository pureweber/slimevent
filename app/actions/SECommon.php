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

	static function generate_select_option($select, $name = ''){
		$o = "";
		foreach($select as $v)
			if(isset($_POST[$name]) && $_POST[$name] == $v['id'])
				$o .= "<option selected value='{$v['id']}'>{$v['name']}</option>";
			else
				$o .= "<option value='{$v['id']}'>{$v['name']}</option>";

		if($name == '')
			return $o;
		else
			F3::set("select_".$name, $o);
	}

	static function upload_img($name){
		$img = $_FILES[$name];
		$img_ext = substr(strrchr($img['name'], '.'), 1);
		$dest_dir = F3::get('UPLOAD_IMG_DIR');

		$dest = $dest_dir.md5(time()).'.'.$img_ext;

		$state = move_uploaded_file($img['tmp_name'], $dest);
		
		$save_path = F3::get('WEB_ROOT').$dest;
		return $save_path;
	}

	static function get_create_form_value(){
		$date = explode('/', F3::get("POST.date"));
		$year = $date[0];
		$month = $date[1];
		$day= $date[2];

		$begin_time = explode(':', F3::get("POST.begin_time"));
		$end_time = explode(':', F3::get("POST.end_time"));

		$d = array();

		$d['title'] = F3::get("POST.title");
		$d['region'] = F3::get("POST.region");
		$d['addr'] = F3::get("POST.addr");
		$d['category'] = F3::get("POST.category");
		$d['lable'] = F3::get("POST.lable");
		$d['introduction'] = F3::get("POST.introduction");

		$d['poster'] = self::upload_img("poster");

		$d['post_time'] = time();
		$d['begin_time'] = mktime($begin_time[0], $begin_time[1], 0, $month, $day, $year);
		$d['end_time'] = mktime($end_time[0], $end_time[1], 0, $month, $day, $year);

		return $d;
	}
}
