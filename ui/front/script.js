
/*
 ***** API for this request *****

 {{ url|raw }}

 ***** API for this request *****
*/

{% for key,value in get %}
var {{ key }} = "{{ value }}";
{% endfor %}

if (typeof ADSSCRIPT === "undefined") {
	var ADSSCRIPT = [];
}
var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9+/=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/rn/g,"n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}


ADSSCRIPT['ad{{ data["script_num"] }}'] = (function () {

	return function () { //Public function
		var default_options = {
			"debugging":false,			// to enable debugging. check console
			"advert":"", 				// advert ID either 123 or md5 of the ID
			"keywords":"",				// keywords the site wants to pass to ad-server
			"mustContain":"",			// adverts must contain these keywords
			"mustNotContain":"",		// adverts must NOT contain these keywords
			"onLoad":function(data){},		// function executed as the script is called
			"onAdvert":function(data,script){},	// function executed right after the adverts script has been called	(when an advert is found)
			"onEmpty":function(data){},		// function executed when no advert is found
			"onEnd":function(data){},		// function executed when the script has finished loading the advert and cleanup
		};
		var options = Object.assign(default_options,arguments[2]);
		debugging_function("ADVERT",options,"group");
		debugging_function("API: {{ url|raw }}",options,"info");
		debugging_function("SCRIPT: {{ url_script|raw }}",options,"info");

		debugging_function(options,options,"info");


		var _THIS = this;

		options.siteID = "{{ data['site']['siteKey'] }}";
		options.module = "{{ data['module']['type'] }}";
		if (options.onLoad && typeof(options.onLoad) === "function") {
			debugging_function("onLoad",options,"group");
			options.onLoad.call(_THIS);
			debugging_function("onLoad",options,"groupEnd");
		}

		var data = {{ data_encoded|raw }};

		if (data.advert && data.advert.ID) {
			debugging_function("ADVERT FOUND",options,"group");

			!(function(options,data) {
				var _SCRIPT = this;
				var advert = data.advert;
				debugging_function("{{ module }} SCRIPT", options, "group");
				debugging_function(advert, options);

				var container = document.createElement("div");
				container.className = "ad-server-block";

//	container.innerHTML += "<img src='{{_domain}}/" + options.module + "/" + options.siteID + "/" + advert.key + "/banner/1px_transparent.png' style='width:1px; height:1px;' />";
				if (options.debugging) {
					console.log("-------------------------------")
					console.info(Base64.decode("{{ display|raw }}").replace(/"/g, '&quot;'));
					console.log("-------------------------------")
				}

				container.innerHTML = Base64.decode("{{ display|raw }}");

				var _THIS = _SCRIPT.parentNode.insertBefore(container, _SCRIPT.nextSibling);

				//console.log(startJs())


				if (options.onAdvert && typeof(options.onAdvert) === "function") {
					debugging_function("onAdvert",options,"group");
					options.onAdvert.call(_THIS, data, _SCRIPT);

					debugging_function("onAdvert",options,"groupEnd");
				}


				{% if display_js %}
				var scriptNode   = document.createElement('script');
				scriptNode.innerHTML  = Base64.decode("{{ display_js|raw }}");
				_SCRIPT.appendChild( scriptNode );
				{% endif %}



				debugging_function("{{ module }} SCRIPT",options,"groupEnd");
			}).call(_THIS, options, data);




			debugging_function("ADVERT FOUND",options,"groupEnd");
		} else {
			debugging_function("NO ADVERT FOUND",options,"group");
			if (options.onEmpty && typeof(options.onEmpty) === "function") {
				debugging_function("onEmpty",options,"group");

				options.onEmpty.call(_THIS,data);

				debugging_function("onEmpty",options,"groupEnd");

			}
			debugging_function("NO ADVERT FOUND",options,"groupEnd");
		}


		if (options.onEnd && typeof(options.onEnd) === "function") {
			debugging_function("onEnd",options,"group");

			options.onEnd.call(_THIS, data);

			debugging_function("onEnd",options,"groupEnd");
		}


		debugging_function("ADVERT",options,"groupEnd");
	}
})();
var timers = {};
function debugging_function(str,options,type){
	if (options.debugging){
		switch(type) {
			case "group":
				timers[str] = performance.now();
				console.group(str);
				break;
			case "groupEnd":

				if (timers[str]){
					var n = performance.now();
					console.log(str + ": "+ (n -timers[str]) );
				}

				console.groupEnd(str);
				break;
			case "error":
				console.error(str);
				break;
			case "info":
				console.info(str);
				break;
			default:
				console.log(str);
		}

	}

};
if (typeof Object.assign != 'function') {
	Object.assign = function (target, varArgs) { // .length of function is 2
		'use strict';
		if (target == null) { // TypeError if undefined or null
			throw new TypeError('Cannot convert undefined or null to object');
		}

		var to = Object(target);

		for (var index = 1; index < arguments.length; index++) {
			var nextSource = arguments[index];

			if (nextSource != null) { // Skip over if undefined or null
				for (var nextKey in nextSource) {
					// Avoid bugs when hasOwnProperty is shadowed
					if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
						to[nextKey] = nextSource[nextKey];
					}
				}
			}
		}
		return to;
	};
};
