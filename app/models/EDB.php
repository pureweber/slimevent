
<?php

class EDB{

	/**
	 * @brief : 向数据表table_name中插入一条记录，data是一个关联数组，键名为字段名，值为字段的值
	 */
	static function insert($table_name, $data){
		$sql="INSERT INTO `".$table_name."` ";
		$v=''; $n='';
		$tmp_data = array();

		foreach($data as $key=>$val)
	    {
			$n.="`$key`, ";
			$v.= ":$key, ";
			$tmp_data[":$key"] = $val;
		}
	
		$sql .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";

		return DB::sql($sql, $tmp_data);
	}

};

?>
