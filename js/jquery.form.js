/*!
 * jQuery Form Plugin
 * version: 3.09 (02-SEP-012)
 * @requires jQuery v1.3.2 or later
 *
 * Examples and documentation at: http://malsup.com/jquery/form/
 * Project repository: https://github.com/malsup/form
 * Dual licensed under the MIT and GPL licenses:
 *    http://malsup.github.com/mit-license.txt
 *    http://malsup.github.com/gpl-license-v2.txt
 */
;(function($) {
"use strict";

/*
    Usage Note:
    -----------
    Do not use both ajaxSubmit and ajaxForm on the same form.  These
    functions are mutually exclusive.  Use ajaxSubmit if you want
    to bind your own submit handler to the form.  For example,

    $(document).ready(function() {
        $('#myForm').on('submit', function(e) {
            e.preventDefault(); // <-- important
            $(this).ajaxSubmit({
                target: '#output'
            });
        });
    });

    Use ajaxForm when you want the plugin to manage all the event binding
    for you.  For example,

    $(document).ready(function() {
        $('#myForm').ajaxForm({
            target: '#output'
        });
    });
    
    You can also use ajaxForm with delegation (requires jQuery v1.7+), so the
    form does not have to exist when you invoke ajaxForm:

    $('#myForm').ajaxForm({
        delegation: true,
        target: '#output'
    });
    
    When using ajaxForm, the ajaxSubmit function will be invoked for you
    at the appropriate time.
*/

/**
 * Feature detection
 */
var form, method, data;


function ajax(form, callback) {
    $.ajax({
        "url"       : form.attr('action') + "?r" + Math.random(),
        "type"      : method ? method : form.attr('method'),
        "dataType"  : data ? data : "json",
        "data"      : form.serialize(),
        "success"   : function(ret) {
            callback(ret);
        }
	});
}


/**
 * ajaxSubmit() provides a mechanism for immediately submitting
 * an HTML form using AJAX.
 */

$.fn.ajax = function(callback, params, method, data) {
    /*jshint scripturl:true */
    form    = $(this);
    data    = data;
    method  = method;

    if (!callback) {
        callback = function() {}
    }

    ajax(form, callback);
};

})(jQuery);
