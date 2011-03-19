/*
 * 基础函系列 - a_cookie_xxx()
 *
 *  a_cookie_set(name, value)
 *	设置cookie
 *
 *  a_cookie_get(name)
 *	返回cookie中name的值
 *
 * */


// 设置cookie
var a_cookie_set = function(name, value) {
    document.cookie = name + "="+ escape (value) + ";";
};


// 获取cookie的值
var a_cookie_get = function(name) {
    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));

     return arr != null ? unescape(arr[2]) : null;
};
