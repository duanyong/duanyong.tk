/*
 * 基础函系列 - a_form_xxx()
 *
 *  a_form_serialize(form)
 *	返回表单域中不为空的所有数据项
 *
 *  a_form_input_value(input)
 *	返回input所对应的值。包括text, password, radio, checkbox
 *
 *  a_form_error(element)
 *	表单项错误，红色显示错误的提示框并黄色显示其提示框
 *
 *  a_form_tip(element, error)
 *	表单项提示，指定error显示黄色的外框提示用户
 *
 *  a_form_onfocus(element)
 *	表单项获取焦点，去除元素上的错误样式
 *
 *  a_form_onblur(element)
 *	表单项推动焦点，根据元素的类型检查其值
 *	    dtype   : (string, numeric)数据的类型
 *	    min	    : 最小值(numeric)或者最小长度(string)
 *	    max	    : 最大值(numeric)或者最大长度(string)
 *
 * */


// 将表单域中不为空的值结合成json返回
var a_form_serialize = function(form) {
    if (a_bad_element( form = a_$(form) )) {
	return a_log();
    }

    var elements;

    if (a_bad_array( elements = form.elements ) ) {
	return {};
    }

    // 对form中的各个元素的name取值，根据类型设置返回对应的值
    for (var name, value, ret={}, len=elements.length-1; len>=0; --len) {
	if (a_bad_element( element = elements[len] )
		|| !( name  = element.name )
		|| !( tname = element.tagName )
	   ) {
	    continue;
	}

	tname = tname.toLowerCase();

	if (tname === "input") {
	    // 简单值
	    value = a_form_input_value(element);

	} else if (tname === "select") {
	    return "select";
	}

	if (!value) {
	    continue;
	}

	ret[name] = value;
    }

    return a_bad_object(ret) ? false : ret;
};


 // 返回input所对应的值。包括text, password, radio, checkbox
var a_form_input_value = function(input) {
    if (!( input = a_$(input) )
	    || input.tagName.toLowerCase() !== "input"
       ) {
	return a_log();
    }

    var type = input.type.toLowerCase();

    if (type === "text"
	    || type === "file"
	    || type === "hidden"
	    || type === "password"
       ) {
	return input.value;

    } else if (type === "radio"
	    || type === "checkbox"
	    ) {
	return input.checked ? input.value : false;
    }

    return false;
};


// 表单项获取焦点，将其的红色外框去除同时也去除其黄色的提示框
var a_form_onfocus = function(element) {
    if (!( element = a_$(element) )) {
	return a_log();
    }

    a_form_error(element, false);
}


// 表单项失去焦点，检查其值是否符合条件
var a_form_onblur = function(element) {
    if (!( element = a_$(element) )) {
	return a_log();
    }

    var min   = element.min ? element.min : 0,
	max   = element.max ? element.max : 99999,
	dtype = element.dtype ? element.dtype : "string";


    if (dtype === "string") {
	// 是字符串，检查长度
	dtype = element.value.length >= min && element.value.length <= max;

    } else if (dtype === "numeric") {
	// 是数字，检查区间
	dtype = !a_bad_number(element.value) && element.value >= min && element.value <= max;
    }

    // 根据返回值需要对应的提示信息
    a_form_error(element, dtype);
}


// 表单元素错误，显示错误的输入框
var a_form_error = function(element, error) {
    if (!( element = a_$(element) )) {
	return a_log();
    }

    var cls = element.className ? element.className : "";

    if (error !== false) {
	// 错误项，红色边框提示
	element.className = cls.indexOf("field_error") < 0 ? cls + " field_error" : "field_error";

    } else {
	// 正确项， 去除红色边框提示
	element.className = cls.indexOf("field_error") > 0 ? cls.replace("field_error", "") : cls;
    }

    // 显示提未项
    a_form_tip(element, error !== false);
}


// 显示表单元素的提示信息
var a_form_tip = function(element, error) {
    var tip, cls;

    if (!( element = a_$(element) )
	    || !( tip = element.id + "_tip")
       ) {
	return false;
    }

    cls = tip.className ? tip.className : "";

    if (!!error) {
	// 需要显示错误项
	cls = cls.indexOf("tip_error") < 0 ? cls + " tip_error" : "tip_error";

    } else {
	// 不需要显示错误的信息
	cls = cls.indexOf("tip_error") > 0 ? cls.replace("tip_error", "") : cls;
    }

    if (cls !== tip.className) {
	tip.className = cls;
    }
}
