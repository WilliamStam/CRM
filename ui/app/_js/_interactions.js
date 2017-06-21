$(document).ready(function(){
	$(document).on("click",".record-interaction",function(){
		var id = $(this).attr("data-id");
		alert("interaction: "+id)

	});

})