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
};

?>
