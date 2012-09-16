<?php

class SEEvent{

	function asd(){
		echo $_POST["title"];
		echo $_POST["sort"];
		echo $_POST["label"];
		echo $_POST["introduction"];
		$a =Event::createevent($_POST["title"],$_POST["sort"],$_POST["label"],$_POST["location"],$_POST["starttime"],$_POST["endtime"],$_POST["introduction"]);
	}
	function create(){
		echo Template::serve('create.html');
	}
	function show(){
		F3::set('route', array('discover', 'intro'));
		$event=Event::getevent(F3::get('PARAMS.eventID'));
		F3::set('event',$event[0]);

		echo Template::serve('event/event.html');
	}
	function photos(){
		F3::set('route', array('discover', 'photo'));

		echo Template::serve('event/photo.html');
	}
	function discussion(){
		F3::set('route', array('discover', 'discussion'));

		echo Template::serve('event/discussion.html');
	}
};

?>
