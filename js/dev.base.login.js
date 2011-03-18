// 检查有没有登录
var a_badLogin = function() {
    // 通过取得cookie，检查cookie是否在有郊期内
    
    var mobile	    = a_cookie_get("mobile"),
	password    = a_cookie_get("password");

    if (!( mobile )
	    || !( password)

	    || mobile.length
	    || mobile.length !== 11

	    || password.length
	    || password.length !== 32
       ) {
	return true;
    }

    return false;
}

a.mobileTip	= "请输入11位的手机号码";
a.passwordTip	= "请输入你在这儿的密码";

var a_minLogin = function(msg) {
    if (!( msg )) {
	msg = "";
    }

    // 获取屏幕的中心点

    var id = "minlogin",
	login;

    if (( login = a_$(id) )) {
	// 已经存在，说明已经在页面上创建过了
	// 1、是否需要更新新消息
	// 2、检查是否在公开的环境中

	return a_show(login);
    }

    login	= document.createElement("div");
    login.id	= "minLogin";

    var str = "";
    str += '<span>' + msg + '</span>';
    str += '手机号码：<input type="text" name="mobile" maxlength="11" value="' + a.mobileTip + '" onclick="a_login_mobile_focus(event);" onblur="a_login_mobile_check(event);" /><br />';
    str += '用户密码：<input type="text" name="password" maxlength="12" value="' + a.passwordTip + '" onfocus="a_login_password_focus(event);" onblur="a_login_password_check(event);" /><br />';
    str += '<input type="submit" value="登录" onclick="a_login_submit()" />';

    login.innerHTML = str;

    return document.body.appendChild(login);
}

var a_login_mobile_focus = function(e) {
    var input = a_event_target(e ? e : window.event);

    if (input.value === a.mobileTip) {
	input.value = "";
    }
}

var a_login_password_focus = function(e) {
    var input = a_event_target(e ? e : window.event);


    if (input.value === a.passwordTip) {
	input.value = "";

	// 设置密码属性，防止输入时出现明文字母
	input.type = "password";
    }
}

var a_login_mobile_check = function(e) {
    e = e ? e : window.event;

    var input = a_event_target(e);

    if (input.value === "") {
	// 用户没有输入任何数字

	input.type  = "text";
	input.value = a.mobileTip;

	input.className = "err";
    }

    return false;
}

// 检查密码是否在12位以内
var a_login_password_check = function(e) {
    var input = a_event_target(e ? e : window.event);

    if (input.value === "") {
	// 用户没有输入任何数字

	input.type  = "text";
	input.value = a.passwordTip;

	input.className = "err";
    }

    return false;
}

var a_exit = function() {}

var a_login = function() {}

var a_return = function() {}

var a_login_submit = function() {
    var mobile	    = a_$("mobile"),
	password    = a_$("password");

    if (!mobile.value) {
	return alert("请输入手机号码");
    }

    if(!password.value) {
	return alert("请输入密码");
    }

    // 禁止用户再次修改表单项
    mobile.disabled	= true;
    password.disabled	= true;

    // 发服务器发送登录消息
    a_ajax({
	    "url"	: "/login.php",
	    "param"	: "mobile=" + mobile.value + "&password=" + password.value,
	    "onfail"	: a_login_fail,

	    "refere"	: "/"
	    });
}

// 用户登录失败
var a_login_fail = function(obj) {
    // 显示提示信息


    var mobile	    = a_$("mobile")
	password    = a_$("password");

    // 将禁止输入的状态打开，让用户修改错误信息
    mobile.disabled	= "";
    password.disabled	= "";
}
