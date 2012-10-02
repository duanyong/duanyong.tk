function s_reg_submit() {
    var form, username, password, agreement;
    if (!( username = $('#rusername').val() )) {
    }

    if (!( password = $('#rpassword').val() )) {

    }

    if (!( agreement = $('#ragreement:checked').val() )) {

    }

    $("#reg_form").ajax(function(ret) {
        if (ret.error === 0) {
            //注册成功
        }
    });

    return false;
}


function s_login_submit() {
    var form, username, password, agreement;
    if (!( username = $('#username').val() )) {

    }

    if (!( password = $('#password').val() )) {

    }

    $("#login_form").ajax(function(ret) {
        console.log(ret);
    });

    return false;
}


$(function() {
    $('#btn_nav_login').on('click', function() {
        $("#reg_dialog").pop();
    });

    $('#login_submit').on('click', s_login_submit);


    $('#btn_nav_reg').on('click', function() {
        $("#reg_dialog").pop();
    });

    $('#reg_submit').on('click', s_reg_submit);

});
