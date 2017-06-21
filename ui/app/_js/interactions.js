$(document).ready(function () {

	$(document).on("change", "#toolbar input[name='view']", function () {
		getData();
	});
	$(document).on("click", ".btn-filter-mine", function (e) {
		e.preventDefault();
		var $this = $(this);
		$.bbq.pushState({"mine":$this.attr("data-value")})
		getData();
	});
	$(document).on("click", ".btn-refresh", function (e) {
		e.preventDefault();
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
	$(document).on("click", ".options-groups", function (e) {
		e.preventDefault();
		var $this = $(this);
		$.bbq.pushState({"groupby":$this.attr("data-value")})
		getData();
	});
	$(document).on("click", ".options-groups-order", function (e) {
		e.preventDefault();
		var $this = $(this);
		$.bbq.pushState({"groupby_dir":$this.attr("data-value")})
		getData();
	});
	$(document).on("change", "#filter-types", function (e) {
		e.preventDefault();
		var $this = $(this);
		$.bbq.pushState({"type":$this.val()})
		getData();
	});
	$(document).on("change", "#filter-staff", function (e) {
		e.preventDefault();
		var $this = $(this);
		$.bbq.pushState({"staff":$this.val()})
		getData();
	});

	$(document).on("click", ".dropdown-menu .checkbox", function (e) {
		//e.preventDefault();
		e.stopPropagation();
	});
	$(document).on("change", ".dropdown-menu .checkbox input", function (e) {
		$("#options-save-columns").removeClass("hidden");
	});
	$(document).on("click", "#options-save-columns", function (e) {
		var str = [];
		$("#options-columns input[type='checkbox']:checked").each(function(){
			var $this = $(this);
			str.push($(this).val());
		})
		str = str.join(",");
		$.bbq.pushState({"columns":str})

		getData();


	});

	$(document).on("click", ".options-order", function (e) {
		e.preventDefault();
		var $this = $(this);
		$.bbq.pushState({"order":$this.attr("data-value")})
		if ($this.hasClass("active")){
			var dir = "DESC";
			switch($this.attr("data-dir")){
				case "DESC":
					dir = "ASC";
					break;
				default:
					dir = "DESC";
					break;

			}
			$.bbq.pushState({"order_dir":dir})

		}

		getData();
	});






	getData()


	
});

function getData() {
	var uri = $.bbq.getState()||{};



	$.getData("/app/data/interactions/data", uri, function (data) {
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






		var ranges = {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		};



		$('#reportrange').daterangepicker({
			ranges: ranges,
			locale: {
				format: 'YYYY-MM-DD'
			}
		}, cb).on("apply.daterangepicker",function (ev, picker) {

			var daterange = $("#toolbar #daterange").val();
			$.bbq.pushState({"daterange":daterange})

			getData();
		});

		function cb(start, end, label, loaded) {
			if (start && end){
				var daterange_val = start.format("Y-MM-DD")+" to "+end.format("Y-MM-DD");
			}
			if (label&&label!="Custom Range"){
				daterange_val = label;
			}
			if (loaded){
				daterange_val = loaded;
			}
			$("#toolbar #daterange").val(daterange_val);
			$('#reportrange span').html(daterange_val);
		}

		if (data.options.daterange){
			var parts,start,end;
			if (data.options.daterange.indexOf(" to ")>0){
				parts = data.options.daterange.split(" to ");
				start = moment(parts[0],"YYYY-MM-DD");
				end = moment(parts[1],"YYYY-MM-DD");
			} else {
				parts = ranges[data.options.daterange];
				start = parts[0];
				end = parts[1];
			}
			if (moment(start,"YYYY-MM-DD").isValid()) $('#reportrange').data('daterangepicker').setStartDate(start);
			if (moment(end,"YYYY-MM-DD").isValid()) $('#reportrange').data('daterangepicker').setEndDate(end);
		}



		cb(false, false, false, data.options.daterange);



		//$("#search").searchbox();

		$(window).trigger("resize");
	},"data")

}




