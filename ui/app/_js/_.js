;


$(document).ready(function () {

	resize();
	$(window).on('resize', function () {
		$.doTimeout(250, function () {
			resize();

		});
	});

	$(window).scroll(function (event) {
		scroll();
		// Do something
	});

	$(".select2").select2();
	$(document).on("change", ".has-error input", function () {
		var $field = $(this);
		$field.closest(".has-error").removeClass("has-error").find(".form-validation").remove();
		submitBtnCounter($field.closest("form"));
	});


	$(document).ajaxComplete(function (event, request, settings) {
		if (request.responseJSON && request.responseJSON.stats) {
			var data = request.responseJSON.stats

			//console.log(data);
			//data.label = $("<div />").append($('#heading-header-bar-label').clone()).html();

			//console.log(data.label)


			//$("#heading-header-bar").jqotesub($("#template-heading-bar-stats"), data);
//			$("#heading-header-bar-stats").jqotesub($("#template-heading-bar-stats"),data);


			//line_chart("dayview", data.day.labels, data.day.all, data.day.unique, true);


		}
	});





});


function copyText(text){
	function selectElementText(element) {
		if (document.selection) {
			var range = document.body.createTextRange();
			range.moveToElementText(element);
			range.select();
		} else if (window.getSelection) {
			var range = document.createRange();
			range.selectNode(element);
			window.getSelection().removeAllRanges();
			window.getSelection().addRange(range);
		}
	}
	var element = document.createElement('DIV');
	element.textContent = text;
	document.body.appendChild(element);
	selectElementText(element);
	document.execCommand('copy');
	element.remove();
}
var Base64 = {
	_keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", encode: function (e) {
		var t = "";
		var n, r, i, s, o, u, a;
		var f = 0;
		e = Base64._utf8_encode(e);
		while (f < e.length) {
			n = e.charCodeAt(f++);
			r = e.charCodeAt(f++);
			i = e.charCodeAt(f++);
			s = n >> 2;
			o = (n & 3) << 4 | r >> 4;
			u = (r & 15) << 2 | i >> 6;
			a = i & 63;
			if (isNaN(r)) {
				u = a = 64
			} else if (isNaN(i)) {
				a = 64
			}
			t = t + this._keyStr.charAt(s) + this._keyStr.charAt(o) + this._keyStr.charAt(u) + this._keyStr.charAt(a)
		}
		return t
	}, decode: function (e) {
		var t = "";
		var n, r, i;
		var s, o, u, a;
		var f = 0;
		e = e.replace(/[^A-Za-z0-9+/=]/g, "");
		while (f < e.length) {
			s = this._keyStr.indexOf(e.charAt(f++));
			o = this._keyStr.indexOf(e.charAt(f++));
			u = this._keyStr.indexOf(e.charAt(f++));
			a = this._keyStr.indexOf(e.charAt(f++));
			n = s << 2 | o >> 4;
			r = (o & 15) << 4 | u >> 2;
			i = (u & 3) << 6 | a;
			t = t + String.fromCharCode(n);
			if (u != 64) {
				t = t + String.fromCharCode(r)
			}
			if (a != 64) {
				t = t + String.fromCharCode(i)
			}
		}
		t = Base64._utf8_decode(t);
		return t
	}, _utf8_encode: function (e) {
		e = e.replace(/rn/g, "n");
		var t = "";
		for (var n = 0; n < e.length; n++) {
			var r = e.charCodeAt(n);
			if (r < 128) {
				t += String.fromCharCode(r)
			} else if (r > 127 && r < 2048) {
				t += String.fromCharCode(r >> 6 | 192);
				t += String.fromCharCode(r & 63 | 128)
			} else {
				t += String.fromCharCode(r >> 12 | 224);
				t += String.fromCharCode(r >> 6 & 63 | 128);
				t += String.fromCharCode(r & 63 | 128)
			}
		}
		return t
	}, _utf8_decode: function (e) {
		var t = "";
		var n = 0;
		var r = c1 = c2 = 0;
		while (n < e.length) {
			r = e.charCodeAt(n);
			if (r < 128) {
				t += String.fromCharCode(r);
				n++
			} else if (r > 191 && r < 224) {
				c2 = e.charCodeAt(n + 1);
				t += String.fromCharCode((r & 31) << 6 | c2 & 63);
				n += 2
			} else {
				c2 = e.charCodeAt(n + 1);
				c3 = e.charCodeAt(n + 2);
				t += String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63);
				n += 3
			}
		}
		return t
	}
};


toastr.options = {
	"closeButton": true,
	"debug": false,
	"newestOnTop": false,
	"progressBar": false,
	"positionClass": "toast-top-center",
	"preventDuplicates": true,
	"onclick": null,
	"showDuration": "300",
	"hideDuration": "1000",
	"timeOut": "3000",
	"extendedTimeOut": "1000",
	"showEasing": "swing",
	"hideEasing": "linear",
	"showMethod": "show",
	"hideMethod": "hide"
};
toastr.options['positionClass'] = 'toast-bottom-right';


$.fn.modal.Constructor.prototype.enforceFocus = function () {};

function line_chart(container, labels, data_all, data_unique, header) {

	var ctx = document.getElementById(container).getContext("2d");

	var grid_color = "rgba(235,235,235,0.4)";
	var grid_zeroLineColor = "rgba(235,235,235,1)";
	var grid_fontColor = "rgba(170,170,170,0.8)";
	var grid_showTooltips = true;

	if (header) {
		var grid_showTooltips = false;
		var grid_color = "rgba(235,235,235,0.2)";
		var grid_zeroLineColor = "rgba(235,235,235,0.4)";
		var grid_fontColor = "rgba(170,170,170,0.6)";

	}


	return new Chart(ctx, {
		type: 'line',
		data: {
			labels: labels,
			datasets: [
				{
					label: "Unique",
					fill: true,
					backgroundColor: "rgba(189,226,165,0.6)",
					backgroundColor: "#bde2a5",
					borderColor: "rgba(98,203,49,0.2)",
					pointBorderColor: "#fff",
					pointBackgroundColor: "rgba(98,203,49,0.5)",
					pointHoverBackgroundColor: "#fff",
					pointHoverBorderColor: "rgba(26,179,148,1)",
					data: data_unique

				},
				{
					label: "All",

					fill: true,
					backgroundColor: "rgba(235,235,235,0.6)",
					borderColor: "rgba(200,200,200,0.2)",
					pointBorderColor: "#fff",
					pointBackgroundColor: "rgba(170,170,170,0.5)",
					pointHoverBackgroundColor: "#fff",
					pointHoverBorderColor: "rgba(170,170,170,1)",

					data: data_all
				}

			]
		},
		options: {
			scaleShowGridLines: true,
			scaleGridLineColor: "rgba(235,235,235,0.4)",
			scaleGridLineWidth: 1,
			bezierCurve: true,
			bezierCurveTension: 0.4,
			pointDot: true,
			pointDotRadius: 17,
			pointDotStrokeWidth: 1,
			pointHitDetectionRadius: 20,
			datasetStroke: true,
			datasetStrokeWidth: 1,
			datasetFill: true,
			animation: false,
			showTooltips: grid_showTooltips,
			responsive: true,
			maintainAspectRatio: false,
			hover: {
				onHover: function(e) {
					$("#"+container).css("cursor", e[0] ? "pointer" : "default");
				}
			},
			legend: {
				display: false,
				labels: {
					display: false
				}
			},
			tooltips: {
				mode: 'label'
			},
			scales: {
				xAxes: [{
					ticks: {
						fontSize: 9,
						fontColor: grid_fontColor,
					},
					gridLines: {
						tickMarkLength: 2,
						color: grid_color,
						zeroLineColor: grid_zeroLineColor
					},
				}],
				yAxes: [{
					ticks: {
						fontSize: 9,
						fontColor: grid_fontColor,

					},
					gridLines: {
						tickMarkLength: 2,
						color: grid_color,
						zeroLineColor: grid_zeroLineColor
					},
				}],

			}
		}
	});


}




function resize() {
	var wh = $(window).height();
	var ww = $(window).width();
	var mh = wh - $("#navbar-header").outerHeight() - 6;
	$("#menu-bar").css({"max-height": mh});
	scroll();

	$(".panel-fixed:not(.tab-panel)").each(function () {
		var $this = $(this);
		var h = $this.find("> .panel-heading").outerHeight()||0;
		var f = $this.find("> .panel-footer").outerHeight()||0;
		var $body = $this.find("> .panel-body");
		$body.css({top: h, bottom: f});
		$this.addClass("fixed")
	});
	$(".panel-fixed.tab-panel").each(function () {
		var $this = $(this);
		var h = $this.find("> .tab-pane > .panel-heading").outerHeight();
		var f = $this.find("> tab-pane > .panel-footer").outerHeight();
		var $body = $this.find("> tab-pane >.panel-body");
		$body.css({top: h, bottom: f});
		$this.addClass("fixed")
	});
}
function scroll() {
	var ww = $(window).width();
	var $toolbar = $("#toolbar");

	if ($toolbar.length) {


		var toolbartop = $toolbar.offset().top;
		var navbarheight = $(".navbar-fixed-top").outerHeight();
		var toolbarheight = $toolbar.outerHeight();
		var scrollTop = $(window).scrollTop();

		$nextElement = $toolbar.next();

		var contentOffset = $nextElement.offset().top;

		var toolboxtopscroll = (contentOffset - toolbarheight) - 15;

		//	console.log("toolbartop: "+toolbartop+" | navbarheight: "+navbarheight+" | scroll:"+scrollTop + " | toolbar fixed: "+$toolbar.hasClass("fixed")+" | v:"+toolboxtopscroll);

		if ((scrollTop > (toolboxtopscroll - navbarheight)) && ww > 768) {
			$toolbar.addClass("fixed").css({"top": navbarheight});
			$nextElement.css({"margin-top": $toolbar.outerHeight() + 31});
		} else {
			$toolbar.removeClass("fixed");
			$nextElement.css({"margin-top": 0});
		}

	}

}




function validationErrors(data, $form) {

	if (!$.isEmptyObject(data['errors'])) {

		var i = 0;
		//console.log(data.errors);
		$(".form-validation", $form).remove();
		$.each(data.errors, function (k, v) {
			i = i + 1;
			var $field = $("#" + k);
			//console.info(k)
			var $block = $field.closest(".form-group");

			$block.addClass("has-error");
			if ($field.parent().hasClass("input-group")) $field = $field.parent();


			if (v != "") {

				$field.after('<span class="help-block s form-validation">' + v + '</span>');
			}
			if ($block.hasClass("has-feedback")) {
				$field.after('<span class="fa fa-times form-control-feedback form-validation" aria-hidden="true"></span>')
			}


		});


		$("button[type='submit']", $form).addClass("btn-danger").html("(" + i + ") Error(s) Found");

		if (i > 1) {
			toastr["error"]("There were " + i + " errors saving the form", "Error");
		} else {
			toastr["error"]("There was an error saving the form", "Error");
		}


	} else {
		toastr["success"]("Record Saved", "Success");

	}

	//submitBtnCounter($form);


}

function submitBtnCounter($form) {
	var c = $(".has-error", $form).length;
	var $btn = $("button[type='submit']", $form);
	if (c) {
		$btn.addClass("btn-danger").html("(" + c + ") Error(s) Found");
	} else {

		var tx = $btn.attr("data-text") || "Save";

		$btn.html(tx).removeClass("btn-danger");
	}
}


var datetimepickerOptions = {
	inline: true,
	sideBySide: true,
	format: "YYYY-MM-DD HH:mm:00",
	icons: {
		time: "fa fa-clock-o",
		date: "fa fa-calendar",
		up: "fa fa-arrow-up",
		down: "fa fa-arrow-down",

		previous: 'fa fa-chevron-left',
		next: 'fa fa-chevron-right',
		today: 'fa fa-screenshot',
		clear: 'fa fa-trash',
		close: 'fa fa-remove'
	}
};
$(document).ready(function () {

	$("body").addClass("load-font")




});

