// 检查有没有登录
var a_bad_login = function() {
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


var a_login_min = function(msg) {
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
    str += '<span></span>';
    str += '<input type="text" name="mobile" maxlength="11" />';
    str += '<input type="password" name="password" maxlength="12" />';
    str += '<input type="submit" value="登录" />';

    login.innerHTML = str;

    document.body.appendChild(login);
}
