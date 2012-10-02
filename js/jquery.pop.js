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
var top, pop, mask,
    isie6 = $.browser.msie && $.browser.version === '6.0';


function hide(event) {
    pop.hide();
    mask.hide();
}


function popReset(event){
    pop.css("left", Math.floor($(window).width() / 2 - pop.outerWidth() / 2));
    //pop.css("top", isie6 ? $(document).scrollTop() : Math.floor($(window).width() / 2 - pop.outerWidth() / 2));
    pop.css("top", !isie6 ? top : Math.floor($(window).width() / 2 - pop.outerWidth() / 2));

    /*
    if (!isie6) {
        pop.css("top", top);

    } else{
        pop.css("top", $(document).scrollTop() + top);
    }
    */
}

function maskReset(event){
    mask.width($(window).width())
        .height($("body").height() > $(window).height() ? $("body").height() : $(window).height());
}


/**
 * ajaxSubmit() provides a mechanism for immediately submitting
 * an HTML form using AJAX.
 */

$.fn.pop = function(popy, fade) {
    /*jshint scripturl:true */
    pop     = $(this);
    top     = top   || 120;
    fade    = fade  || 250;
    close   = pop.find(".close");	
    mask    = $(".overlay");	

    var position;
    if (isie6) {
        position = 'absolute';
    } else {
        position = 'fixed';
    }

    mask.css({
        "top"       : 0,
        "left"      : 0,
        "position"  : position
    });

    mask.show();	

    pop.css({"position": position});

    if (fade <= 1) {
        pop.show();
    } else {
        pop.fadeIn(fade);
    }

    popReset();
    maskReset();

    close.one('click', hide);
};


$.fn.popOff = function() {
    $(this).trigger('close');

    pop     = null;
    mask    = null;
    close   = null;
};

})(jQuery);
