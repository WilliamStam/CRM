$(document).ready(function () {


	getData()


	
});
function getData() {
	var key =  $.bbq.getState("key");
	var active = $("input[name='active-filter']:checked").val();

	$.getData("/app/data/home/data", {'key':key}, function (data) {

		var stats_data = data['charts'];
		$("#content-area").jqotesub($("#template-content"), data);




		$(window).trigger("resize");
	},"data")

}




