var a_index_init = function () {
    // 绑定login、quit、return事件
    var element;
    if (( element = a_$("login") )) {
	element.onclick = a_login;
    }

    if (( element = a_$("exit") )) {
	element.onclick = a_exit;
    }

    if (( element = a_$("return") )) {
	element.onclick = a_return;
    }

    // 检查是否登录
    if (a_bad_login()) {
	// 显示登陆同时隐藏退出和返回
	a_show("login");
	a_hide("exit", "return");

    } else {
	// 显示退出和返回同时隐藏登录
	a_hide("login");
	a_show("exit", "return");
    }
}

var a_index_login = function() {
    if (a_bad_login()) {
	// 用户并没有登录，显示登陆框即可

	return false;
    }

    // 页面跳转
    return ;
}

a_index_init();
a_index_login();
