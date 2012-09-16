
<?php

class Event{

	static function getevent($id)
	{
    return $r=DB::sql("select * from event where id= :id",array(':id' => $id));
	}
	static function createevent($title,$sort,$label,$location,$starttime,$endtime,$introduction)
	{
	 DB::sql("insert into event(title,sort,label,location,starttime,endtime,introduction) values(:title,:sort,:label,:location,:starttime,:endtime,:introduction)",array(':title' => $title,':sort' => $sort,':label' => $label,':location'=>$location,':starttime'=>$starttime,':endtime'=>$endtime,':introduction' => $introduction));
	}
};

?>
