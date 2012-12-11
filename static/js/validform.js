$(document).ready(function () {
	$(".validate-required").blur(resetErrorMsg);
	$(".validate-time-order").blur(resetErrorMsg);
	$(".validate-date-before-now").blur(resetErrorMsg);
	$(".validate-required-upload").click(resetErrorBtn);
});

var success = true;

function validate(){
	success = true;

	$(".validate-required").each(function(){
		if($(this).val() != '')
			$(this).parentsUntil(".contorl-group").removeClass("error");
		else{
			$(this).parentsUntil(".contorl-group").addClass("error");
			success = false;
		}
	});

	$(".validate-time-order").each(function(){
		if(checkTime("begin_time", "end_time"))
			$(this).parentsUntil(".contorl-group").removeClass("error");
		else{
			$(this).parentsUntil(".contorl-group").addClass("error");
			success = false;
		}
	});

	$(".validate-date-before-now").each(function(){
		if(checkDateBeforeNow("date"))
			$(this).parentsUntil(".contorl-group").removeClass("error");
		else{
			$(this).parentsUntil(".contorl-group").addClass("error");
			success = false;
		}
	});

	$(".validate-required-upload").each(function(){
		if($(this).find(".fileupload").hasClass("fileupload-exists"))
			$(this).find(".btn").removeClass("btn-danger");
		else{
			$(this).find(".btn").addClass("btn-danger");
			success = false;
		}
	});

	return success;
}

function checkDateBeforeNow(dateClass){
	var dateStr = $("input[name='"+dateClass+"']").val();
	var t = dateStr.split('-');
	var d = new Date(), today = new Date();
	d.setFullYear(t[0], t[1] - 1, t[2]);

	if(d.getTime() > today.getTime())
		return true;
	else
		return false;
}


function checkTime(begin, end){
	var begin_time = $("input[name='"+begin+"']").val();
	var end_time = $("input[name='"+end+"']").val();

	if(getMinutes(begin_time) < getMinutes(end_time))
		return true;
	else
		return false;
}

function getMinutes(time){
	var d = new Date();
	var t = time.split(':');
	d.setHours(t[0], t[1]);
	return d.getTime();
}

function resetErrorMsg(){
	//it.parentsUntil(".contorl-group").removeClass("error");
	$(this).parentsUntil(".contorl-group").removeClass("error");
}
function resetErrorBtn(){
	//it.parentsUntil(".contorl-group").removeClass("error");
	$(this).find(".btn").removeClass("btn-danger");
}

