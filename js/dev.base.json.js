/*
 * 基础函系列 - a_json_xxx()
 *
 *  a_json_string(json)
 *	返回把json转换成url的字符串
 *
 * */

var a_json_string = function(json, name) {
    var type, value, ret = [];

    if (a_type(json) === "object") {
	for (var key in json) {

	    type = a_type( value = json[key] );

	    if (type === "object"
		|| type === "array"
	       ) {
		ret += a_json_string(value, name);

		continue;
	    }

	    ret.push(key + "=" + value);
	}

    } else if (type === "array") {
	// 是数组
	if (a_type(name) !== "string") {
	    return "";
	}

	for (var len=json.length; len>=0; --len) {
	    ret.push(name + "=" +json[len]);
	}
    }

    return ret.join("&");
};
