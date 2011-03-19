/*
 * 基础函系列 - a_login_xxx()
 *	登陆做为基础的函数放置在dev.base.js中。因为登录是用户用到最多的功能，所以集成到base中
 *
 *  a_login_show(msg)
 *	用户需要进行验证操作时，创建一个迷你登录框供用户登录。
 *
 *  a_login_mobile_focus(e)
 *	手机输入框获取焦点，清除登录框中的提示文字
 *
 *  a_login_password_focus(e)
 *	密码输入框获取焦点，清除登录框中的提示文字
 *
 *  a_login_mobile_check(e)
 *	登录时对手机的格式做检查
 *
 * */
// 手机的提示信息
a.mobileTip	= "请输入11位的手机号码";

// 密码的提示信息
a.passwordTip	= "请输入你在这儿的密码";


var a_login_show = function(msg) {
    if (!( msg )) {
	msg = "";
    }

    // 获取屏幕的中心点

    var id = "minlogin",
	ui;

    if (( ui = a_$(id) )) {
	// 已经存在，说明已经在页面上创建过了
	// 1、是否需要更新新消息
	// 2、检查是否在公开的环境中

	return a_show(ui);
    }

    ui	= document.createElement("div");
    ui.id	= "minLogin";

    var str = "";
    str += '<span>' + msg + '</span>';
    str += '手机号码：<input type="text" name="mobile" maxlength="11" value="' + a.mobileTip + '" onclick="a_form_focus(event);" onblur="a_login_mobile_check(event);" /><br />';
    str += '用户密码：<input type="text" name="password" maxlength="12" value="' + a.passwordTip + '" onfocus="a_login_password_focus(event);" onblur="a_login_password_check(event);" /><br />';
    str += '<input type="submit" value="登录" onclick="a_login_submit()" />';

    ui.innerHTML = str;

    return document.body.appendChild(ui);
};


// 用户点击手机号码输入框，显示隐藏提示内容
var a_login_mobile_focus = function(e) {
    var input = a_event_target(e ? e : window.event);

    if (input.value === a.mobileTip) {
	input.value = "";
    }
};


// 密码框获取焦点后清除提示语句
var a_login_password_focus = function(e) {
    var input = a_event_target(e ? e : window.event);


    if (input.value === a.passwordTip) {
	input.value = "";

	// 设置密码属性，防止输入时出现明文字母
	input.type = "password";
    }
};


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
