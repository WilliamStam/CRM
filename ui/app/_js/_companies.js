$(document).ready(function(){
	$(document).on("click",".record-company",function(){
		var id = $(this).attr("data-id");
		//alert("Individual: "+id)
		$.bbq.pushState({"details":"company-"+id});
		getCompanyDetails();
	});
	getCompanyDetails();
});
function getCompanyDetails(){
	var id = $.bbq.getState("details");

	if (id){
		id = id.split("-");
		if (id[0]=="company"){
			$.getData("/app/data/contacts/company",{"ID":id[1]},function(result){

				//console.log(result.template)
				$("#modal-window").jqotesub(result.template,result).modal("show").on("hide.bs.modal",function(){
					$.bbq.pushState({"details":""})
				})

			})
		}
	}

}