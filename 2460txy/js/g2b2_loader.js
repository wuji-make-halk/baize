(function() {
	var errtimes = 0;

	function loadScript(url, callback) {
		var script = document.createElement("script");
		script.type = "text/javascript";

		script.onload = function() {
			callback && callback();
		};
		script.onerror = function() {
			script.parentNode.removeChild(script);
			if (errtimes < 2) {
				loadScript('https://js.2460.xileyougame.com/js/jssdk/g2b2.js?v=' + new Date().getTime());
			} else {
				setTimeout(function() {
					loadScript(url + new Date().getTime());
				}, 1000);
			}
			errtimes++;
		};
		script.src = url // + '?v=' + gameVersion;
		document.getElementsByTagName("head")[0].appendChild(script);
	}
	loadScript('https://js.2460.xileyougame.com/js/jssdk/g2b2.js?v=' + new Date().getTime());
})();
