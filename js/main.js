//检查用户的账号及昵称是否注册
function s_reg_has() {
    if ($('#rnickname').val()
        || $('#rusername').val()
    ) {
        //查看服务器是否已注册
        $.getJSON('has.php',  $("#reg_form").serialize(), function(ret) {
            if (ret.username) {
                return $('#rusername').wrong('您的账号已被注册');
            }

            if (ret.nickname) {
                return $('#rusername').wrong('您的昵称已被注册');
            }
        });
    }
}


//提交用户注册信息
function s_reg_check() {

    if (!$('#rusername').val()) {
        return $('#rusername').wrong('您可以用邮箱或者手机号来登录');
    }

    if (!$('#rpassword').val()) {
        return $('#rpassword').wrong('为了您账号的安全请输入密码');
    }

    if (!$('#rnickname').val()) {
        return $('#rnickname').wrong();
    }


    return true;
}


//用户登录
function s_login_check() {
    var form, username, password;

    if (!( username = $('#username').val() )) {
        return $('#username').wrong();
    }

    if (!( password = $('#password').val() )) {
        return $('#password').wrong('请输入您的登录密码');
    }

    return true;
}



$(function() {
    $('#reg_form').human();
    $('#login_form').human();

    $('#btn_nav_login').on('click', function() {
        $("#login_dialog").pop();
    });

    $('#login_submit').on('click', s_login_check);


    $('#btn_nav_reg').on('click', function() {
        $("#reg_dialog").pop();
    });

    $('#reg_submit').on('click', s_reg_check);

    //检查用户账号及用户昵称
    $('#rusername').on('blur', s_reg_has);
    //$('#rnickname').on('blur', s_reg_has);

});
