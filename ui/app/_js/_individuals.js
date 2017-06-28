$(document).ready(function(){
	$(document).on("click",".record-individual",function(e){
		e.preventDefault();
		var id = $(this).attr("data-id");
		//alert("Individual: "+id)
		$.bbq.pushState({"details":"individual-"+id});
		getIndividualDetails();
	});

	$(document).on("click",".form-individual",function(e){
		e.preventDefault();
		var id = $(this).attr("data-id");
		//alert("Individual: "+id)
		$.bbq.pushState({"form":"individual-"+id});
		getIndividualForm();
	});

	$(document).on("submit","#individual-form",function(e){
		e.preventDefault();
		var $this = $(this);
		var data = $this.serialize();
		var ID = $this.attr("data-id");
		$.post("/app/save/contacts/individual?ID="+ID,data,function(result){
			result = result.data;
			validationErrors(result, $this);
			if (!result.errors) {
				getData();
			}
		})

	});
	getIndividualDetails();
	getIndividualForm();
});


function getIndividualDetails(){
	var id = $.bbq.getState("details");

	if (id){
		id = id.split("-");
		if (id[0]=="individual"){
			$.getData("/app/data/contacts/individual",{"ID":id[1]},function(result){

				//console.log(result.template)
				$("#modal-window").jqotesub($("#template-modal-individuals"),result).modal("show").on("hide.bs.modal",function(){
					$.bbq.pushState({"details":""})
				})

			})
		}
	}

}
function getIndividualForm(){
	var id = $.bbq.getState("form");
	if (id){
		id = id.split("-");
		if (id[0]=="individual"){
			$.getData("/app/data/contacts/individual_form",{"ID":id[1]},function(result){

				//console.log(result.template)
				$("#modal-window").jqotesub($("#template-modal-individuals-form"),result).modal("show").on("hide.bs.modal",function(){
					$.bbq.pushState({"form":""})
				})

			})
		}
	}

}