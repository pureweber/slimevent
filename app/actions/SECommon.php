<?php
/**
 * Common
 *
 * @package   Slimevent
 **/

class SECommon{

	static function set_unread_msg_num(){
		//$unread_msg_num = MsgBox::get_unread_num();
		F3::set("unread_msg", 3);
	}

	static function generate_select_option($select, $name = ''){
		$o = "";
		$n = array();

		foreach($select as $v){
			if(is_array($v))
				$n[$v['id']] = $v['name'];
		}

		foreach($n as $k => $v)
			if(isset($_POST[$name]) && $_POST[$name] == $k)
				$o .= "<option selected value='{$k}'>{$v}</option>";
			else
				$o .= "<option value='{$k}'>{$v}</option>";

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
		//Code::dump($_POST);
		$year = $date[0];
		$month = $date[1];
		$day= $date[2];

		$begin_time = explode(':', F3::get("POST.begin_time"));
		$end_time = explode(':', F3::get("POST.end_time"));

		$d = array();

		$eid = F3::get("POST.eid");


		if($eid !== false){
			$d['eid'] = (int)$eid;
		}
		//Code::dump($d);
		//echo $d['eid'];

		if(F3::get("POST.poster_change") == 1)
			$d['poster'] = self::upload_img("poster");
		//echo $d['poster'];
		//echo F3::get("POST.upload_change");

		$d['title'] = F3::get("POST.title");
		$d['region'] = F3::get("POST.region");
		$d['addr'] = F3::get("POST.addr");
		$d['category_id'] = F3::get("POST.category");
		$d['lable'] = F3::get("POST.lable");
		$d['introduction'] = F3::get("POST.introduction");

		$d['post_time'] = time();
		$d['begin_time'] = mktime($begin_time[0], $begin_time[1], 0, $month, $day, $year);
		$d['end_time'] = mktime($end_time[0], $end_time[1], 0, $month, $day, $year);

		return $d;
	}

	static function format_time_to_show($info){
		$info['date'] = date("Y/m/d", $info['begin_time']);
		$info['begin_time'] = date("H:i", $info['begin_time']);
		$info['end_time'] = date("H:i", $info['end_time']);

		return $info;
	}


	static function format_info_to_show($info){

		// format Time&Date
		$info = self::format_time_to_show($info);

		// format Region
		$region = F3::get("REGION");

		foreach($region as $k => $v){
			if($info['region'] == $k){
				$info['region'] = $v;
				break;
			}
		}

		return $info;
	}

}

