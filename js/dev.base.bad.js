/*
 * 基础函系列 - a_bad_xxx()
 *
 *  a_bad_element(element)
 *	返回是否为dom元素
 *
 *  a_bad_num(num)
 *	返回是否为一个数字
 *
 *  a_bad_string(string)
 *	返回是否为一个字符串
 *  
 *  a_bad_array(arr)
 *	返回是否为一个代数组
 *
 *  a_bad_object(obj)
 *	返回是否为空json对象
 *
 *  a_bad_function(func)
 *	返回是否为函数
 *
 *  a_bad_login()
 *	返回是否登录
 * */


// 检查是否为dom元素
var a_bad_element = function(element) {
    return a_type( element = a_$(element) ) !== "element";
};


// 检查是否为数字
var a_bad_number = function(num) {
    return !(/^\d+$/.test(num));
};


// 检查是否为空字符串
var a_bad_string = function(str) {
    return a_type(str) !== "string" && str === "";
};


// 检查是否为空数组
var a_bad_array = function(arr) {
    return a_type(arr) !== "array" && arr.length === 0;
};


// 检查是否为空对象
var a_bad_object = function(obj) {
    if ( a_type(obj) !== "object" ) {
	return true;
    }

    for (var k in obj) {
	return false
    }

    return true;
};


// 检查是否为函数
var a_bad_function = function(func) {
    return a_type(func) !== "function";
};


// 检查用户是否登录
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
};
