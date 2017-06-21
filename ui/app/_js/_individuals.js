$(document).ready(function(){
	$(document).on("click",".record-individual",function(){
		var id = $(this).attr("data-id");
		//alert("Individual: "+id)
		$.bbq.pushState({"details":"individual-"+id});
		getIndividualDetails();
	});
	getIndividualDetails();
});
function getIndividualDetails(){
	var id = $.bbq.getState("details");

	if (id){
		id = id.split("-");
		if (id[0]=="individual"){
			$.getData("/app/data/contacts/individual",{"ID":id[1]},function(result){

				//console.log(result.template)
				$("#modal-window").jqotesub(result.template,result).modal("show").on("hide.bs.modal",function(){
					$.bbq.pushState({"details":""})
				})

			})
		}
	}

}