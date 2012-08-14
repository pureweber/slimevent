<?php

class SEEvent{
	function show(){
		F3::set('route', array('discover', 'intro'));

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
