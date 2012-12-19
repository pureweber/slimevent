<?php
/**
 * Common
 *
 * @package   Slimevent
 **/

class SECommon{

	static $lock = true;	

	function __construct()
	{
		if(self::$lock === true)
		{
			LogFile::add(); 
			self::$lock = false;
		}
	}

	private function _get_instance(){
		$group = Account::the_user_group();
		$class = "SE".ucfirst(strtolower($group));

		return new $class();
	}

	function my(){
		$gay = $this->_get_instance();
		$gay->my();
	}

	 function set_unread_msg_num(){
		//$unread_msg_num = MsgBox::get_unread_num();
		F3::set("unread_msg", 3);
	}

	 function generate_select_option($select, $name = ''){
		$o = "";
		$n = array();

		foreach($select as $v){
			if(is_array($v))
				$n[$v['id']] = $v['name'];
			else
				$n = $select;
		}
		//Code::dump($n);
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

	 function upload_img($name){
		$img = $_FILES[$name];
		$img_ext = substr(strrchr($img['name'], '.'), 1);
		$dest_dir = F3::get('UPLOAD_IMG_DIR');

		$dest = $dest_dir.md5(time()).'.'.$img_ext;

		$state = move_uploaded_file($img['tmp_name'], $dest);
		
		$save_path = F3::get('WEB_ROOT').$dest;
		return $save_path;
	}

	/**
	 * 将一个年月日小时分钟 转换为时间戳
	 * @param $date Y-m-d
	 * @param $time H:m
	 * @return timestmp 时间戳
	 */
	function change_date_to_timestamp($date, $time)
	{
		return strtotime($date.' '.$time);
	}

	 function get_create_form_value(){
		$d = array();

		$begin_date = F3::get("POST.begin_date");
		$end_date = F3::get("POST.end_date");

		$begin_time = F3::get("POST.begin_time");
		$end_time = F3::get("POST.end_time");

		$d['begin_time'] = $this->change_date_to_timestamp($begin_date, $begin_time);
		$d['end_time'] = $this->change_date_to_timestamp($end_date, $end_time);

		$eid = F3::get("POST.eid");
		if($eid !== false){
			$d['eid'] = (int)$eid;
		}

		if(F3::get("POST.poster_change") == 1)
			$d['poster'] = $this->upload_img("poster");

		$d['title'] = F3::get("POST.title");
		$d['region'] = F3::get("POST.region");
		$d['addr'] = F3::get("POST.addr");
		$d['category_id'] = F3::get("POST.category");
		$d['label'] = F3::get("POST.label");
		$d['introduction'] = F3::get("POST.introduction");
		$d['sign_up'] = F3::get("POST.sign_up");

		return $d;
	}

	 function get_remain_day($aimdate)
	 {
	 	$remainday = (strtotime($aimdate) - strtotime(date("Y-m-d")))/86400;

		if($remainday < -2)
			$exinfo = abs($remainday)."天前";
		else if($remainday == -2)
			$exinfo = "前天";
		else if($remainday == -1)
			$exinfo = "昨天";
		else if($remainday == 0)
			$exinfo = "今天";
		else if($remainday == 1)
			$exinfo = "明天";
		else if($remainday == 2)
			$exinfo = "后天";
		else 
			$exinfo = $remainday."天后";

		return $exinfo;
	 }

	 //返回当前时间与begin 与 end的时间段的关系  已结束 进行中 未开始
	 /**
	  * @param $begin 开始时间绰
	  */
	 private function get_time_status($begin, $end)
	 {
		if(time() < $begin)
			return F3::get('EVENT_NOT_BEGIN');
		else if(time() > $end)
			return F3::get('EVENT_HAVE_END');
		else 
			return F3::get('EVENT_IS_RUNNING');
	 }

	 function format_time_to_show($info){
	 	//开始结束时间戳
		$info['a_begin_time'] = $info['begin_time'];
		$info['a_end_time'] = $info['end_time'];

		//开始结束时间年月日
		$info['begin_date'] = date("Y-m-d ", $info['begin_time']);
		$info['end_date'] = date("Y-m-d ", $info['end_time']);

		//开始结束星期几 
		$info['begin_weekday'] = F3::get("WEEKDAY.".date("w",strtotime($info['begin_date'])));
		$info['end_weekday'] = F3::get("WEEKDAY.".date("w",strtotime($info['end_date'])));

		//开始结束与今天时间差信息
		$info['begin_remain_day'] = $this->get_remain_day($info['begin_date']);
		$info['end_remain_day'] = $this->get_remain_day($info['end_date']);

		//开始结束具体时间 小时分钟
		$info['begin_time'] = date("H:i", $info['begin_time']);
		$info['end_time'] = date("H:i", $info['end_time']);

		//活动创建时间
		$info['post_time'] = date("Y-m-d H:i:s", $info['post_time']);
		
		//已结束 未开始 进行中
		$info['time_status'] = $this->get_time_status($info['a_begin_time'],$info['a_end_time']);

		return $info;
	}

   /* private function format_short_title_to_show($title)*/
	//{
         //$len = iconv_strlen($title,"UTF-8");
		//$max_len = (int)F3::get("MAX_SHOW_TITLE_LEN");
		//if($len > $max_len)
             //return mb_substr($title,"0",$max_len,"UTF-8"). "...";
		//else
			//return $title;
	/*}*/
	 function format_info_to_show($info){
		// format Time&Date
		$info = $this->format_time_to_show($info);

		// format Region
		$region = F3::get("REGION");
		foreach($region as $k => $v)
			if($info['region'] == $k){
				$info['region'] = $v;
				break;
			}
		// format label
		$info['label'] = explode(' ',$info['label']);

		//$info['short_title'] = $this->format_short_title_to_show($info['title']);

		return $info;
	}

	 function format_infos_to_show($data){
		$d = array();
		foreach($data as $info)
			$d[] = $this->format_info_to_show($info);
		return $d;
	}

	/**
	 * 根据条件直接设置经过处理的结果,同时设置分页
	 * @param $url : 分页的基础url
	 * @param $con : SQL条件
	 * @param $data : SQL条件中的值
	 * @param $set_name : 在模板中的名字
	 * @param $result_num : 返回结果的数量,如果为false(默认)则自动进行分页
	 * @return void
	 */
	 function show_by($url, $con = "", $data = array(),
			 $set_name = "events", $result_num = false){

		if($con == '' && count($data) == 0){// 默认只选择passed的
			$con = "`event`.`status` = :status";
			$data[':status'] = F3::get("EVENT_PASSED_STATUS");
		} else if(stripos($con, 'status') === false){
			// 如果原语句没有判断状态，则默认为只选择passed
			$con .= " AND `event`.`status` = :status";
			$data[':status'] = F3::get("EVENT_PASSED_STATUS");
		}

		if($result_num){
			$num = $result_num - 1;
			$limit = " LIMIT 0, $result_num";
		}else{
			$total_num = Event::get_num($con, $data);
			if($total_num == 0)
				return false;

			$get_page = F3::get("GET.page");
			$per_page_show = F3::get("PER_PAGE_SHOW");
			$current_page = $get_page == NULL ? 0 : $get_page;
			$limit = " LIMIT ".$current_page * $per_page_show.", ".$per_page_show;
		}

		$events = Event::show_by($con.$limit, $data);
		$e = $this->format_infos_to_show($events);

		if(!$result_num)
			$this->pagination($current_page, (int)ceil($total_num / $per_page_show), $url);

		//$result_num = count($e);

		F3::set($set_name, $e);

		return isset($total_num)?$total_num:$result_num;

	}

	/**
	 * 设置分页
	 * @return void
	 */
	 function pagination($current_page, $total_pages, $url = '', $onclick = false){
		$html = "";
		$and = strpos($url, '?') === false ? "?" : "&";
		$html .= "<div class='pagination pagination-right'> <ul>";
		$url = F3::get("WEB_ROOT").$url;
		//$current_page = $current_page == NULL ? 0 : $current_page;

		//
		if($current_page == 0):
			$html .= "<li class='disabled'><a href='#'>上一页</a></li>";
		else:
			$prev_page = $current_page - 1;
			$html .= "<li><a href='";
			$html .= $onclick? "#' onclick='{$onclick}(${prev_page}, this)":"{$url}{$and}page={$prev_page}";
			$html .= "'>上一页</a></li>";
		endif;


		for($i = 0; $i < $total_pages; $i++):
			$j = $i + 1;
			if($i == $current_page):
				$html .= "<li class='active'><a href='#'>{$j}</a></li>";
			else:
				$html .= "<li><a href='";
				$html .= $onclick? "#' onclick='{$onclick}(${i}, this)":"{$url}{$and}page={$i}";
				$html .= "'>{$j}</a></li>";
			endif;
		endfor;


		if($current_page == $total_pages - 1):
			$html .= "<li class='disabled'><a href='#'>下一页</a></li>";
		else:
			$next_page = $current_page + 1;
			$html .= "<li><a href='";
			$html .= $onclick? "#' onclick='{$onclick}(${next_page}, this)'":"{$url}{$and}page={$next_page}";
			$html .= "'>下一页</a></li>";
		endif;

		$html .= "</ul></div>";

		F3::set('pagination', $html);
	}

}

