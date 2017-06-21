$(document).ready(function () {
	$(document).on("click", ".pagination a", function (e) {
		e.preventDefault();
		var $this = $(this);
		$data_page = $this.attr("data-page")||$this.parent().attr("data-page");

		$.bbq.pushState({"page": $data_page});
		$("html, body").animate({ scrollTop: 0 }, 200);
		getData();
	});


	$(document).on("click", ".btn-filter-type", function (e) {
		e.preventDefault();
		var $this = $(this);

		$.bbq.removeState("page");
		$.bbq.pushState({"type":$this.attr("data-value")})
		getData();
	});







	$(document).on("submit", "#search-form", function (e) {
		e.preventDefault();
		$.bbq.pushState({"search":$("#search").val()})
		getData();
	});

	$(document).on("reset", "#search-form", function (e) {
		e.preventDefault();
		$.bbq.pushState({"search":""})
		$("#search").val("");
		getData();
	});
















	getData()


	
});

function getData() {
	var uri = $.bbq.getState()||{};



	$.getData("/app/data/contacts/data", uri, function (data) {

		$("#page-title").text(data.title);
		$.bbq.removeState("groupby");
		$.bbq.removeState("groupby_dir");
		$.bbq.removeState("order");
		$.bbq.removeState("order_dir");
		$.bbq.removeState("columns");
		$.bbq.removeState("type");
		$.bbq.removeState("num_records");


		$("#content-area").jqotesub($("#template-content"), data);


		$(".select2").select2();

		$( "#options-columns" ).sortable({
			axis: "y",
			containment: "parent",
			items: 'li',
			update: function( event, ui ) {
				$("#options-save-columns").removeClass("hidden");
			},
			start: function( event, ui ) {
				$(ui.item[0]).tooltip('hide');
			}
		});
		$( "#options-columns" ).disableSelection();
		$('[data-toggle="tooltip"]').tooltip();
		$('[data-toggle="popover"]').popover();

		$("tables:not(.no-tablesaw)").each(function(){
			var currentTable = $(this);
			currentTable.addClass('tablesaw tablesaw-stack');
			currentTable.attr("data-tablesaw-mode", "stack");
			$(document.body).trigger( "enhance.tablesaw" );
		});



		$(".stack-table").each(function(){
			var $table = $(this);
			var columHeadings = [];

			$("tr",$table).each(function(){
				var $row = $(this);
				if ($row.hasClass('table-columns-row')){
					columHeadings = [];
					$("th",$row).each(function(){
						columHeadings.push($(this).text());
					})
				}
				$("td",$row).each(function(i){
					var $cell = $(this);

					var str = '';
					str += '<div class="mobile-label">'+columHeadings[i]+'</div>'
					str += '<div class="mobile-value">'+$cell.html()+'</div>'
					$cell.html(str);

				})

			})

		});




		$(window).trigger("resize");
	},"data")

}




