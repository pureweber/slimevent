$(document).ready(function () {
	$(".validate-required").blur(resetErrorMsg);
	$(".validate-time-order").blur(resetErrorMsg);
	//$(".validate-date-before-now").blur(resetErrorMsg);
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
		if(checkTime("begin", "end"))
			$(this).parentsUntil(".contorl-group").removeClass("error");
		else{
			$(this).parentsUntil(".contorl-group").addClass("error");
			success = false;
		}
	});

	//$(".validate-date-after-now").each(function(){
		//if(checkDateAfterNow(""))
			//$(this).parentsUntil(".contorl-group").removeClass("error");
		//else{
			//$(this).parentsUntil(".contorl-group").addClass("error");
			//success = false;
		//}
	//});

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

// 判断开始时间小于结束时间，同时均大于当前时间
function checkTime(begin, end){
	var begin_time = $("input[name='"+begin+"_time']").val();
	var begin_date = $("input[name='"+begin+"_date']").val();

	var end_time = $("input[name='"+end+"_time']").val();
	var end_date = $("input[name='"+end+"_date']").val();

	var now = new Date();
	var begin = getTime(begin_date, begin_time);

	if(begin < getTime(end_date, end_time)
			&& now.getTime() < begin)
		return true;
	else
		return false;
}

function getTime(date, time){
	var d = new Date();

	var t = time.split(':');
	d.setHours(t[0], t[1], 0, 0);

	var a = date.split('-');
	d.setFullYear(a[0], a[1] - 1, a[2]);
	
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

