$(document).ready(function () {

	$.getData("/app/test/data", {}, function (data) {


		$("#search").searchbox({
			"data": data
		});

	})


	$(document).on("click", ".content-btn", function () {
		var c = $(this).text();
		$("#search").val(c).trigger("change");


	})

	$(document).on("click", ".content-btn-blank", function () {
		$("#search").val("").trigger("change");


	})


});

;(function ($, window, document, undefined) {
	// Create the defaults once
	var pluginName = "searchbox",
		defaults = {
			data: {
				"smile": {
					"records": [
						{"smile": "tongue", "shortcode": ":stuck_out_tongue:"},
						{"smile": "crazy_tongue", "shortcode": ":stuck_out_tongue_winking_eye:"},
						{"smile": "winking", "shortcode": ":stuck_out_tongue_winking_eye:"},
					],
					"field": "smile",
					"template": "<div data-value='@@smile@@'>@@shortcode@@</div>"
				},
				"test": {
					"records": [
						{"smile": "tongue", "shortcode": ":stuck_out_tongue:"},
						{"smile": "crazy_tongue", "shortcode": ":stuck_out_tongue_winking_eye:"},
						{"smile": "winking", "shortcode": ":stuck_out_tongue_winking_eye:"},
					],
					"field": "smile",
					"template": "<div>@@shortcode@@</div>"
				}
			},
			tagClass: "sb-tag",
			tagValueClass: "sb-value",
			template: "<div>@@item@@</div>",
			maxHeight: 200,
			selectFirstSuggestionOnSpace: true,
		};

	// The actual plugin constructor
	function Plugin(element, options) {
		this.element = element;
		this.options = $.extend({}, defaults, options);

		this._defaults = defaults;
		this._name = pluginName;

		this.init();
	}

	var selectText = function () {
		var range, selection;
		if (document.body.createTextRange) {
			range = document.body.createTextRange();
			range.moveToElementText(this);
			range.select();
		} else if (window.getSelection) {
			selection = window.getSelection();
			range = document.createRange();
			range.selectNodeContents(this);
			selection.removeAllRanges();
			selection.addRange(range);
		}
	};
	Plugin.prototype = {

		init: function () {
			var _this = this;
			_this.element = $(_this.element);
			_this.searchbox = $('<div class="search-box" contenteditable="true"></div>');
			_this.suggestions = $('<div class="search-box-suggestion"><div class="search-box-suggestion-header"></div><div class="search-box-suggestion-items"></div><div class="search-box-suggestion-footer"></div></div>');


			_this.element.on("change", function (e) {
				_this.tagify();
			});


			_this.searchbox.on("keyup", function (e) {
				_this.typing(e);
			});


			_this.suggestions.on("click", ".search-box-suggestion-items > *", function (e) {

				var $this = $(this);
				var val = $this.attr("data-value");
				console.log(val);

				_this.searchbox.data("sb-new-val", val);


				_this.searchbox.get(0).focus();
				_this.suggestions.hide();

			});


			_this.searchbox.on("mousedown", function (e) {
				var $this = $(this);
				var txt = $this.text();


				// check to see if the target is the value tag. if it is then save the lookup type and index to data for later use
				if ($(e.target).hasClass(_this.options.tagClass)) {
					var lookup = $(e.target).attr("data-sb-lookup");
					var i = $(e.target).prevAll("." + _this.options.tagClass + "[data-sb-lookup='" + lookup + "']").length;
					$this.data("sb-select", {"lookup": lookup, "index": i});

				}
				// check to see if the target is the value tag. if it is then save the lookup type and index to data for later use
				if ($(e.target).hasClass(_this.options.tagValueClass)) {
					var lookup = $(e.target).parent().attr("data-sb-lookup");
					var i = $(e.target).parent().prevAll("." + _this.options.tagClass + "[data-sb-lookup='" + lookup + "']").length;
					$this.data("sb-select", {"lookup": lookup, "index": i});

				}

			});


			_this.searchbox.on("focus", function (e) {
				var $this = $(this);
				var txt = $(_this.element).val();
				var pos = $this.caret('pos');

				$this.text(txt);
				$this.caret('pos', pos);
				if ($this.data("sb-select")) {
					var sb_select_data = $this.data("sb-select")

					var curr_pos = $this.caret('pos');

					var end = txt.indexOf(' ', curr_pos);
					end = (end == -1) ? txt.length : end;

					var start = txt.slice(0, end).lastIndexOf(":");

					_this.searchbox.selectText(start + 1, end);

					var v = txt.slice(start + 1, end)

					_this.suggest(sb_select_data['lookup'], v, curr_pos);

					$this.data("sb-select", false)
				}
				if ($this.data("sb-suggest") && $this.data("sb-new-val")) {
					var sb_suggest_data = $this.data("sb-suggest")

					var curr_pos = sb_suggest_data.pos;

					var end = txt.indexOf(' ', curr_pos);
					end = (end == -1) ? txt.length : end;

					var start = txt.slice(0, end).lastIndexOf(":") + 1;


					var v = txt.slice(start, end)

					console.log("suggestion clicked");

					var replace_with = $this.data("sb-new-val");


					console.log({"start": start, "end": end, "length": txt.length, "replace": v, "with": replace_with});


					var s = txt.substring(0, start);
					var e = txt.substring(end, txt.length);
					txt = s + replace_with + e;
					console.info({"s": s, "e": e, "txt": txt})


					/*



					 if (start == txt.length){
					 txt = txt + replace_with;
					 } else {
					 var s = txt.substring(0,start);
					 var e =
					 txt = txt.replace(txt.substring(start,end),replace_with);
					 }
					 */
					$this.text(txt);
					$(_this.element).val(txt);

					$this.caret('pos', start + replace_with.length);
					//$this.text(txt);

					$this.data("sb-suggest", false)
					$this.data("sb-new-val", false)
				}


			});

			_this.searchbox.on("blur", function () {
				_this.tagify();
				setTimeout(function () {
					_this.suggestions.hide();
				}, 100)
				//
			});

			_this.tagify();


			_this.element.wrap('<div class="search-box-container"></div>');
			_this.element.before(_this.searchbox);
			_this.element.after(_this.suggestions);
			//	_this.suggestions.css({"top":_this.searchbox.outerHeight(),"display":"none"});


		},

		tagify: function () {
			var str = $(this.element).val();


			var rendered = str;

			for (var i in this.options.data) {
				var item = i;
				var regexstring = item + ":([^-\\s]*)";
				var regexp = new RegExp(regexstring, "g");
				var myArray;
				while ((myArray = regexp.exec(str)) !== null) {
					var matStr = myArray[0];
					var val = matStr.replace(item + ":", "");
					var s = item + ":" + '<span class="' + this.options.tagValueClass + '">' + val + '</span>';
					rendered = rendered.replace(myArray[0], '<span class="' + this.options.tagClass + '" data-sb-lookup="' + item + '">' + s + '</span>')
				}
			}

			//this.searchbox.html(str);
			this.searchbox.html(rendered);
		},
		typing: function (event) {
			var _this = this;
			_this.element = $(_this.element);
			var txt = _this.searchbox.text();
			//this.searchbox.text(this.searchbox.text())

			console.log("begining: [" + txt + "] | keycode: [" + event.keyCode + "]");


			var curr_pos = _this.searchbox.caret('pos');
			//	console.log(curr_pos)


			var s = "";
			var str = txt;
			var pos = curr_pos;

			// Search for the word's beginning and end.
			var left = str.slice(0, pos).search(/\S+$/),
				right = str.slice(pos).search(/\s/);

			// The last word in the string is a special case.
			if (right < 0) {
				s = str.slice(left);
			} else {
				s = str.slice(left, right + pos);
			}




			if (!event.shiftKey) {

				if (s.indexOf(":") != -1) {
					var parts = s.split(":");
					var lookup = parts[0];
					var val = parts[1];

					_this.suggest(lookup, val, curr_pos);



					//console.log("typing lookup | lookup:"+lookup+" | value:"+val);
				} else {
					_this.suggestions.hide();
				}


				if (_this.options.selectFirstSuggestionOnSpace) {
					console.log(pos)

					if (event.keyCode == 32) {
						pos = pos - 1;
						// Search for the word's beginning and end.
						left = str.slice(0, pos).search(/\S+$/);
						right = str.slice(pos).search(/\s/);

						// The last word in the string is a special case.
						if (right < 0) {
							s = str.slice(left);
						} else {
							s = str.slice(left, right + pos);
						}



						console.log("key:" + event.keyCode + " | specialcase:" + s)

						if (s.indexOf(":") != -1) {
							var parts = s.split(":");
							var lookup = parts[0];
							var val = parts[1];


							console.log({
								"lookup":lookup,
								"val":val,
							})

							var results = $.grep(_this.options.data[lookup].records, function( n, i ) {
								var text = n[_this.options.data[lookup]['field']].replace(/\s+/g, ' ').toLowerCase();


								var ret = true;
								if (val){
									ret = text.indexOf(val)=!-1;
									console.log(ret)
								}
								console.log("VAL: "+val+" | TEXT:"+text+" | RET:"+ret+" | indexOf:"+text.indexOf(val))
								return ret;
							});
							var newval = results[0][_this.options.data[lookup]['field']];




							console.log(results)



							if (newval && (val == "")) {
								console.log("new val:" + newval)

								var stxt = str.slice(0, pos),
									etxt = str.slice(pos + 1, str.length);


								txt = stxt + newval + " " + etxt + "";

								_this.searchbox.text(txt);
								var newpos = stxt + newval;
								newpos = newpos.length;


								//newpos = newpos > txt.length ? txt.length : newpos;

								_this.searchbox.caret('pos', newpos);
								_this.searchbox.caret('pos', _this.searchbox.caret('pos')+1);

								console.log("check pos:" + _this.searchbox.caret('pos'))

								console.log({
									"stxt": stxt,
									"newval": newval,
									"etxt": etxt,
									"txt": txt,
									"pos": pos,
									"newpos": newpos,
									"newval len": newval.length,
									"txt len": txt.length
								})


							}


						}

					}
				}
			}



			console.log("saving: [" + txt + "]");
			console.log("--------------------------");
			_this.element.val(txt);
			//	_this.searchbox.text(txt);


			//var start = txt.slice(0,end).lastIndexOf(":");

		},
		suggest: function (lookup, val, curr_pos) {
			var _this = this;
			var _items = _this.suggestions.find(".search-box-suggestion-items")
			var _header = _this.suggestions.find(".search-box-suggestion-header")
			var _footer = _this.suggestions.find(".search-box-suggestion-footer")
			//

			_this.searchbox.data("sb-suggest", {"lookup": lookup, "pos": curr_pos});

			if (typeof  _this.options.data[lookup] != "undefined") {
				var lookup_data = _this.options.data[lookup];
				_header.html("looking up <strong>" + lookup + "</strong> for <strong>" + val + "</strong>");

				var content = lookup_data.records.map(function (d) {
					var temp = lookup_data.template || _this.options.template;
					for (var i in d) {
						var replaceWhat = "@@" + i + "@@";
						replaceWhat = replaceWhat.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
						var re = new RegExp(replaceWhat, 'g');
						temp = temp.replace(re, d[i]);
					}

					temp = $('<div/>').html(temp).contents();
					temp.attr("data-value", d[lookup_data['field']]);
					temp = temp.prop('outerHTML');
					return temp
				});

				_items.html(content)


				val = $.trim(val).replace(/ +/g, ' ').toLowerCase();
				var $rows = _items.children();

				var count = 0;
				$rows.show().filter(function () {
					var text = $(this).attr("data-value").replace(/\s+/g, ' ').toLowerCase();
					if (text.indexOf(val) != -1) count = count + 1;
					return !~text.indexOf(val);
				}).hide();

				_footer.html("found: <strong>" + count + "</strong>");
				_this.suggestions.show();


			}


		}

	};

	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function (options) {
		return this.each(function () {
			if (!$.data(this, "plugin_" + pluginName)) {
				$.data(this, "plugin_" + pluginName,
					new Plugin(this, options));
			}
		});
	};

})(jQuery, window, document);
jQuery.fn.selectText = function (start, end) {
	var range, selection;
	return this.each(function () {
		if (document.body.createTextRange) {
			range = document.body.createTextRange();
			range.moveToElementText(this);
			range.moveStart("character", start);
			range.collapse(true);
			range.moveEnd("character", end);


			range.select();
		} else if (window.getSelection) {
			selection = window.getSelection();
			range = document.createRange();
			//range.selectNodeContents(this);
			range.setStart(this.firstChild, start);
			range.setEnd(this.firstChild, end);
			selection.removeAllRanges();
			selection.addRange(range);
		}
	});
};
