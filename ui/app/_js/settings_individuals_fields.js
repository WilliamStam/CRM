var content_blocks = {};
var codemirrors = {};
$(document).ready(function() {

	$(document).on("click",'#type-btns .btn',function(e){
		e.preventDefault();
		var $this = $(this);


		var val = $this.attr("data-value");
		var $content = $("#content-zone-area");
		var content =  cleanup();





		//console.log(content)
		//console.log($content.data("content"))

		var cont_ = true;
		if (content != $content.data("content")){
			if (confirm("Save changes first?") == true) {
				cont_ = false;
				$.bbq.pushState({"renderer":val})
				$("#form").submit();

			} else {
				cont_ = true;
			}
		}



		if (cont_){
			$("#type-btns .btn").removeClass("btn-primary").addClass("btn-default");
			$this.addClass("btn-primary").removeClass("btn-default");

			$.getData("/app/data/settings_individuals_fields/_render",{"renderer":val},function(response){
				$content.html(response.content);
				$("#content-zone-area").data("content",cleanup());
				$("#______renderer______").val(response.renderer);
				$("#content-zone .modal-footer").jqotesub($("#template-modal-individuals-footer-"+response.renderer),response);

				if ($("#content-zone .modal-footer .footer-mask")!=1){
					$("#content-zone .modal-footer").append($("<div>").addClass("footer-mask"))
				}

				setupDrag()
			})
		}
	});


	$(document).on("submit", "#form", function(e) {
		e.preventDefault();
		$("#______content______").val(cleanup());
		var $this = $(this);
		var data = $this.serialize();

		$.post("/app/save/settings_individuals_fields/form",data,function(result){
			result = result.data;
			validationErrors(result, $this);
			if (!result.errors) {
				getData();
			}
		})
	});
	$(document).on("dblclick", "#content-zone-area .item ", function(e) {
		e.preventDefault();
		var $this = $(this);
		var $item = $this.closest(".item");
		var id = $item.attr("data-item");

		if (id){
			$.bbq.pushState({"module":id});
			getModule();
		}

	});
	$(document).on("dblclick", "#list-items .list-group-item", function(e) {
		e.preventDefault();
		var $this = $(this);
		var id = $this.attr("data-id");
		if (id){
			$.bbq.pushState({"module":id});
			getModule();
		}
	});


	$(document).on("click", ".btn-filter-type", function(e) {
		e.preventDefault();
		var $this = $(this);

		$.bbq.removeState("page");
		$.bbq.pushState({"section": $this.attr("data-value")})
		getData();
	});
	$(document).on("click", "#btn-trash", function(e) {
		e.preventDefault();
		$("#______content______").val("");
		var renderer = $("#type-btns .btn-primary").attr("data-value");
		$("#content-zone-area").jqotesub($("#template-content-area-blank-"+renderer),{});
		setupDrag();
	});



	getData();
	getModule();

	$(".select2").select2();


	$(document).on("change", "#select-field-types", function(e) {
		$.bbq.pushState({"type": $(this).val()})
		getList()
	});
	$(document).on("submit", "#filter-form", function(e) {
		e.preventDefault();
		$.bbq.pushState({"search": $("#search").val()})
		getList()
	});
	$(document).on("reset", "#filter-form", function(e) {
		e.preventDefault();
		$("#search").val("");
		$.bbq.pushState({"search": ""});
		getList()
	});


	$(document).on("click", ".btn-new-field", function(e) {
		var type = $("#select-field-types").val();

		alert("new btn:" + type);
	});


	$(window).on("scroll resize", function() {
		sideMenu()
	});

	$(document).on('change', '[data-toggle="buttons"] input:radio', function() {
		var $this = $(this).closest("label");
		var $parent = $this.parent();
		$parent.find(".btn").removeClass("btn-primary").addClass("btn-default")
		$this.removeClass("btn-default").addClass("btn-primary");

	});
	$(document).on('submit', '#resource-item-form', function(e) {
		e.preventDefault();
		var $this = $(this);
		var data = $(this).serialize();

		$.post("/app/save/settings_individuals_fields/resource",data,function(result){
			result = result.data;
			validationErrors(result, $this);
			if (!result.errors) {
				$("#form").submit();
				$this.closest(".modal").modal("hide");
			}
		});
		console.log(data)

	});

});
function getModule(){
	var id = $.bbq.getState("module");
	codemirrors = {};
	if (id){
		$.getData("/app/data/settings_individuals_fields/resource",{"ID":id},function(response){
			$("#modal-window").jqotesub($("#template-content-item-modal"), response).modal("show").on("hide.bs.modal",function(){
				$.bbq.pushState({"module":""})
			});

			$("#modal-window .modal-body").jqotesub(response.template,response);
		})
	}

}
function setupDrag(){

	$( "#content-zone-area .content-area" ).sortable({
		revert: true,
		connectWith: ".content-area",
		receive: function (event, ui) {
			var id = $(ui.item).attr("data-id");
			var resource = $(ui.item).attr("data-resource");

			var _class = "item"
			if (resource=="layout"){
				_class = "layout-item"
			}

			$(this).find('div.ui-draggable').replaceWith('<div class="'+_class+'" data-item="'+id+'"></div>');


			render()
		}
	});
	$( "#list-items .list-group-item" ).draggable({
		connectToSortable: "#content-zone-area .content-area",
		helper: "clone",
		revert: "invalid"
	});

	$("#list-area").droppable({
		drop: function(event, ui) {
			ui.draggable.remove();
		}
	});
}
function cleanup(){
	var $content = $("#content-zone-area").clone();

	$content.find(".item").empty();
	$content.find("*").removeClass("ui-sortable ui-sortable-handle");


	return $content.html();

}
function render(){

	var content = cleanup();


	$.post("/app/data/settings_individuals_fields/_render",{"content":content},function(response){
		response = response.data;

		var $content = $("#content-zone-area");
		$content.html(response.content);
		setupDrag()
	});
	//console.log(content)
}

function getData() {
	var search = $.bbq.getState("search");

	var rend = "";
	if ($.bbq.getState("renderer")){
		rend = "?renderer="+$.bbq.getState("renderer");
	}

	$.getData("/app/data/settings_individuals_fields/data"+rend, {"search": search}, function(data) {
		$.bbq.removeState("renderer");
		content_blocks = data.content_blocks;

		$("#content-area").jqotesub($("#template-content"), data);
		$("#side-bar-body").jqotesub($("#template-list"), data);

		$("#content-zone-area").data("content",cleanup());
		setupDrag()

		sideMenu();
		if ($("#content-zone .modal-footer .footer-mask")!=1){
			$("#content-zone .modal-footer").append($("<div>").addClass("footer-mask"))
		}
		$(".select2").select2();
		$(window).trigger("resize");
	}, "data")

}
function getList() {
	var ID = $.bbq.getState("ID");
	var search = $.bbq.getState("search");
	var type = $.bbq.getState("type");


	$.getData("/app/data/settings_individuals_fields/_list", {"search": search, "type": type}, function(data) {
		content_blocks = data.content_blocks;

		$("#side-bar-body").jqotesub($("#template-list"), data);
		setupDrag()
		sideMenu();

		$(window).trigger("resize");
	}, "list-data")

}
function sideMenu() {
	var $rightArea = $("#right-area");
	var $sideBar = $("#side-bar");
	var $sideBarBody = $("#side-bar-body");

	var w = $rightArea.width();

	var scroll = $(window).scrollTop();


	if( $(window).width() > 768 ) {
		$rightArea.addClass("fixed");


		var content_start_top = $("#content-start").offset()['top'];

		var fixed_navbars_height = $("#main-nav-bar").outerHeight();
		if( $("#toolbar").outerHeight() ) {
			fixed_navbars_height = fixed_navbars_height + $("#toolbar").outerHeight();
		}

		var top_minus_scroll = content_start_top - scroll;
		var top = (top_minus_scroll < fixed_navbars_height) ? fixed_navbars_height : top_minus_scroll;


		$(".fixed #side-bar").css({width: w, bottom: 0, "top": top, "position": "fixed"});


	} else {
		$rightArea.removeClass("fixed");


		$("#side-bar").swipe({
			swipe: function(event, direction, distance, duration, fingerCount, fingerData) {
				if( $(window).width() < 768 ) {
					console.log(direction)
					if( direction == "right" ) {
						$("#side-bar").find(".offcanvas").trigger("offcanvas.close");
					}
					if( direction == "left" ) {
						$("#side-bar").find(".offcanvas").trigger("offcanvas.open");
					}

				}

			}, threshold: 75, allowPageScroll: "auto"
		}).removeProp("style");


	}


}