function a_ajax(obj) {
    if (a_bad_object(obj)) {
	return a_log();
    }

    if (a_bad_string(obj.url)) {
	return a_log();
    }


    obj.method = obj.method.toLowerCase() === 'get' ? 'get' : 'post';

}

function a_ajax_xmlhttp() {

    var xmlhttp=false;
    /*@cc_on @*/
    /*@if (@_jscript_version >= 5)
    // JScript gives us Conditional compilation, we can cope with old IE versions.
    // and security blocked creation of the objects.
    try {
	xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
	try {
	    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

	} catch (e) {
	    xmlhttp = false;
	}
    }
    @end @*/

    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
	try {
	    xmlhttp = new XMLHttpRequest();
	} catch (e) {
	    xmlhttp=false;
	}
    }

    if (!xmlhttp && window.createRequest) {
	try {
	    xmlhttp = window.createRequest();
	} catch (e) {
	    xmlhttp=false;
	}
    }

    return xmlhttp;
}
