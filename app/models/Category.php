<?php

class Category{
	
	/*
	 * 得到所有分类
	 * @return array
	 */
	static function get_all()
	{
		$sql = "SELECT * FROM `category`";
		return DB::sql($sql);
	}

	static function get_name($id)
	{
		$sql = "SELECT name FROM `category` WHERE id = :id";
		$r = DB::sql($sql, array(':id'=>$id));
		return $r[0]['name'];
	}
};

?>
