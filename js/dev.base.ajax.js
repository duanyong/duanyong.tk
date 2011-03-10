
var a_ajax = function(obj) {
    if (a_bad_object(obj)
	    || a_bad_string(obj.url)
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


var __ajax_callback = function() {
    if (this.readyState !== 4) {
	return ;
    }

    var err	= this.responseText,
	param	= this.__param,
	hascall = a_type(param.callback) === "function";

    if (hascall) {
	// 请求ajax的参数, 返回回来的参数， ajax
	param.callback.call(param, err, this);
    }


    // 自己先判断，看是否是默认的跳转或者其它默认指令
    if (err.err !== 0) {
	// 不正确
	return a_dialog_show(err.msg);
    }

    // 有cookie信息，用js设置到cookie中


    // 需要进行页面跳转
    if (err.referer) {
	// 页面需要跳转
	return __ajax_redirect(err.referer, err.msg, err.delay);
    }

    // 删除ajax对象
    delete this;
}


// ajax取得值后需要进行页面跳转
var __ajax_redirect = function(refere, msg, delay) {
    if (!delay) {
	delay = 3;
    }

    if (!refere) {
	//跳转到首页
	refere = "/";
    }

    if (!msg) {
	msg = "请求成功";
    }

    // 得到一个空的对话框
    var dialog = a_dialog();

    dialog.innerHTML = '还余<span>' + delay + '</span>秒，<a href="' + refere + '">点击此处马上跳转</a>';


    __ajax_countdown();
}

// ajax的计数函数，全站采用统一的风格
var __ajax_countdown =function(count, id) {
}

