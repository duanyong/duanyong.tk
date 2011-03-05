
var a_ajax = function(obj) {
    if (a_bad_object(obj)
	    || a_bad_string(obj.url)
	    || a_type(obj.callback) !== "function"
       ) {
	return a_log();
    }



    var ajax, param, method;

    if (( obj.form = a_$(obj.form) )) {
	param = a_form_serialize(obj.form);
    }

    if (a_type(obj.param) === "object") {
	param = a_merge(obj.param, param);
    }


    if (a_type( ajax = __ajax_http() ) !== "object") {
	return a_log();
    }


    method = obj.method && obj.method.toLowerCase() === 'get' ? 'get' : 'post';

    ajax.onreadystatechange = __ajax_callback;
    ajax.open(method, obj.url, true);

    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8;");

    ajax.send(a_json_string(param));

    ajax.__param = obj;
};


var __ajax_http = function() {

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

    if (!xmlhttp && typeof XMLHttpRequest !== undefined) {
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
};


var __ajax_callback = function(response) {
    if (this.readyState !== 4) {
	return ;
    }

    // 成功回调
    if (this.__param
	    && a_type(this.__param.callback) === "function"
       ) {

	try {
	    this.__param.callback.call(this.__param, this.responseText);
	} catch (e) {}
    }

    // 删除ajax对象
    delete this;
}
