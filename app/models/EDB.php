
<?php

class EDB{

	/**
	 * 把$data数组的下标 x 转换为 :x 
	 * @param $data 
	 * @return false ? array
	 */
	private static function convert_array_index($data)
	{
		$tmp_data = array();

		if(is_array($data) && count($data) > 0)
			foreach($data as $key=>$val)
				$tmp_data[":$key"] = $val;
		else
			return false;

		return $tmp_data;
	}

	/**
	 * @brief : 向数据表table_name中插入一条记录，data是一个关联数组，键名为字段名，值为字段的值
	 */
	static function insert($table_name, $data){
		$sql="INSERT INTO `".$table_name."` ";
		$v=''; $n='';

		if(is_array($data) && count($data) > 0)
			foreach($data as $key=>$val)
	    	{
				$n.="`$key`, ";
				$v.= ":$key, ";
			}
		else
			return false;
	
		$sql .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";

		return DB::sql($sql, self::convert_array_index($data));
	}

	/*
	 * @brief : 更新数据库条目
	 * @desc : 更新数据表table_name中的id_name为id_value的记录，data是一个关联数组，键名为字段名，值为字段的值
	 */
	static function update($table_name, $data, $id_name, $id_value)
	{
		$sql = "UPDATE `$table_name` SET ";

		if(is_array($data) && count($data) > 0)
		{
			foreach($data as $field => $value)
				$sql .= "`$field` = ".":$field".",";

			$sql = rtrim($sql, ', ') . " WHERE `$id_name` = :$id_name";
		}
		else
			return false;

		$data["$id_name"] = $id_value;

		return DB::sql($sql, self::convert_array_index($data));
	}

	/**
	 * 返回table_name里field字段为value的记录个数
	 *
	 */
	static function counts($table_name, $field, $value)
	{
		$sql = "SELECT * FROM `$table_name` WHERE `$field` = :$field";

		$r = DB::sql($sql, array(":$field" => $value));

		return count($r);
	}

	/**
	 * 返回table_name里field字段为value的所有记录关联数组
	 *
	 */
	static function select($table_name, $field, $value)
	{
		$sql = "SELECT * FROM `$table_name` WHERE `$field` = :$field";

		$r = DB::sql($sql, array(":$field" => $value));

		return $r;
	}

	/**
	 * 删除table_name里field字段为value的所有记录关联数组
	 *
	 */
	static function deleted($table_name, $field, $value)
	{
		$sql = "DELETE FROM `$table_name` WHERE `$field` = :$field";

		$r = DB::sql($sql, array(":$field" => $value));

		return $r;
	}
};

?>
