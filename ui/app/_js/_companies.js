$(document).ready(function(){
	$(document).on("click",".record-company",function(e){
		e.preventDefault();
		var id = $(this).attr("data-id");
		//alert("Individual: "+id)
		$.bbq.pushState({"details":"company-"+id});
		getCompanyDetails();
	});

	$(document).on("click",".form-company",function(e){
		e.preventDefault();
		var id = $(this).attr("data-id");
		//alert("Individual: "+id)
		$.bbq.pushState({"form":"company-"+id});
		getCompanyForm();
	});

	$(document).on("submit","#company-form",function(e){
		e.preventDefault();
		var $this = $(this);
		var data = $this.serialize();
		var ID = $this.attr("data-id");
		$.post("/app/save/contacts/company?ID="+ID,data,function(result){
			result = result.data;
			validationErrors(result, $this);
			if (!result.errors) {
				getData();
			}
		})

	});


	getCompanyDetails();
	getCompanyForm();
});


function getCompanyDetails(){
	var id = $.bbq.getState("details");

	if (id){
		id = id.split("-");
		if (id[0]=="company"){
			$.getData("/app/data/contacts/company",{"ID":id[1]},function(result){

				//console.log(result.template)
				$("#modal-window").jqotesub($("#template-modal-companies"),result).modal("show").on("hide.bs.modal",function(){
					$.bbq.pushState({"details":""})
				})

			})
		}
	}

}
function getCompanyForm(){
	var id = $.bbq.getState("form");
	if (id){
		id = id.split("-");
		if (id[0]=="company"){
			$.getData("/app/data/contacts/company_form",{"ID":id[1]},function(result){

				//console.log(result.template)
				$("#modal-window").jqotesub($("#template-modal-companies-form"),result).modal("show").on("hide.bs.modal",function(){
					$.bbq.pushState({"form":""})
				})

			})
		}
	}

}