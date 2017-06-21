$(document).ready(function(){
	$(document).on("click", "#toolbar-settings-form .dropdown-menu .checkbox", function (e) {
		//e.preventDefault();
		e.stopPropagation();
	});

	$(document).on("submit", "#toolbar-settings-form", function (e) {
		e.preventDefault();
		var str = [];
		$("#options-columns input[type='checkbox']:checked").each(function(){
			var $this = $(this);
			str.push($(this).val());
		})
		str = str.join(",");
		$.bbq.pushState({"columns":str,"num_records":$("#num_records").val()})

		getData();


	});
	$(document).on("click", "#toolbar-settings-form .options-groups", function (e) {
		e.preventDefault();
		var $this = $(this);
		$.bbq.pushState({"groupby":$this.attr("data-value")})
		getData();
	});
	$(document).on("click", "#toolbar-settings-form .options-groups-order", function (e) {
		e.preventDefault();
		var $this = $(this);
		$.bbq.pushState({"groupby_dir":$this.attr("data-value")})
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


	$(document).on("click", ".list-content .btn-refresh", function (e) {
		e.preventDefault();
		getData();
	});

})