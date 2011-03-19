/*
 * a_ajax(obj);
 *
 *  obj = {
 *	url	    : (必须)string	    => 提交到服务器的地址
 *	form	    : [可选]form	    => form提交的ID或者是form元素
 *	param	    : [可选]json/string   => 提交的参数
 *	onfail	    : [可选]function	    => 失败后处理的函数
 *	onsucces    : [可选]function	    => ajax成功之后执行的函数
 *  }
 *
 *
 *  1、提交的对象必须为json对象
 *  2、可以不指定onfail/onsuccess等回调函数
 *  3、服务器返回有两种结果
 *	成功 --- 需要页面跳转只需要指定referer在指定时间内跳转，如果指定msg在显示的提示信息
 *	错误 --- 显示错误信息只需要指定msg，会自动显示并在默认的指定时间内隐藏
 *
 * */

var a_ajax = function(obj) {
    if (a_bad_object(obj)
	    || a_bad_string(obj.url)
       ) {
	return a_log();
    }

    // 将obj.form中的元素序列化成一个对象
    var form, parma;

    if (( obj.form = a_$(obj.form) )) {
	form = a_json_string(a_form_serialize(obj.form));
    }

    // 用户是否还指定了参数
    param = a_type(obj.param);

    if (param === "object") {
	// 将form中的对象与用户传进来的对象合并起来
	param = a_json_string(param);

    } else if (param === "string") {
	// 是字符串
	param = obj.param;
    }

    form    = form ? form : "";
    param   = param ? param : "";


    // 取得异步ajax对象，准备向服务器发送数据
    var ajax	= __ajax_http();

    if (a_type(ajax) !== "object") {
	return a_log();
    }

    ajax.onreadystatechange = __ajax_onchangestate;
    ajax.open(obj.method !== 'get' ? 'get' : 'post', obj.url, true);
    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8;");
    ajax.send(param + "&" + form);

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


var __ajax_onchangestate = function() {
    if (this.readyState !== 4) {
	return ;
    }

    var err	= this.responseText,
	param	= this.__param;

    // 自己先判断，看是否是默认的跳转或者其它默认指令
    if (err.err !== 0) {
	// 不正确，唉，真痛苦！！！

	// 是否有消息
	if (err.msg) {
	    a_dialog_show(err.msg);
	}

	if (!a_bad_function(param.onfail)) {
	    param.onfail.call(this, err, param);
	}

	return ;
    }


    // 有cookie信息，用js设置到cookie中
    if (!a_bad_object(err.cookies)) {
	for (var key in err.coo) {
	}
    }

    // 是否有onsuccess回调函数
    if (!a_bad_function(param.onsuccess)) {
	param.onsuccess.call(this.err, param);
    }

    // 需要进行页面跳转
    if (!a_bad_string(err.referer)) {
	// 页面需要跳转
	return __ajax_redirect(err.referer, err.msg, err.delay);
    }

    // 删除ajax对象
    delete this;
};


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
    var confm = a_confirm();

    dialog.innerHTML = '还余<span>' + delay + '</span>秒，<a href="' + refere + '">点击此处马上跳转</a>';


    __ajax_countdown();
};

// ajax的计数函数，全站采用统一的风格
var __ajax_countdown =function(count, id) {
};

