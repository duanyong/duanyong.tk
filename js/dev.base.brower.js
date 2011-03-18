(function() {
    var ua = navigator.userAgent.toLowerCase(),
	UA = ua.match(/(opera|ie|firefox|chrome|version)[\s\/:]([\w\d\.]+)?.*?(safari|version[\s\/:]([\w\d\.]+)|$)/) || [null, 'unknown', 0],

	mode = UA[1] == 'ie' && document.documentMode,
	name = UA[1] == "version" ? UA[3] : UA[1],

	version	    = mode || parseFloat((UA[1] == 'opera' && UA[4]) ? UA[4] : UA[2]),
	platform    = navigator.platform.toLowerCase();

    a.brower_os		= ua.match(/ip(?:ad|od|hone)/) ? 'ios' : (ua.match(/(?:webos|android)/) || platform.match(/mac|win|linux/) || ['other'])[0];
    a.brower_name	= UA[1];
    a.brower_version	= version;
    a.brower_platform	= navigator.platform.toLowerCase();
})();


var a_brower_isIE = function() {
    return a.brower_name === "ie";
}
