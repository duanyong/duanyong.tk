//检查用户的账号及昵称是否注册
function s_reg_has() {
    if ($('#rnickname').val()
        || $('#rusername').val()
    ) {
        //查看服务器是否已注册
        $.getJSON('has.php',  $("#reg_form").serialize(), function(ret) {
            if (ret.username) {
                $('#rusername').wrong('您的账号已被注册');
            }

            if (ret.nickname) {
                $('#rusername').wrong('您的昵称已被注册');
            }
        });
    }
}


//提交用户注册信息
function s_reg_submit() {
    if (!$('#rnickname').val()) {
        return $('#rnickname').wrong();
    }

    if (!$('#rusername').val()) {
        return $('#rusername').wrong('您可以用邮箱或者手机号来登录');
    }

    if (!$('#rpassword').val()) {
        return $('#rpassword').wrong('为了您账号的安全请输入密码');
    }


    if (!$('#ragreement:checked').val()) {
        return $('#ragreement').wrong('请您同意我们的用户协议');
    }

    $("#reg_form").ajax(function(ret) {
        if (ret.error === 0) {
            //注册成功
            $('#login_dialog').popOff();

            //页面跳转
            return window.location.href = "main.html";
        }

        //注册出错，服务器返回的就不用友好提示了
        $('#rusername').wrong(ret.errmsg);
    });

    return false;
}


//用户登录
function s_login_submit() {
    var form, username, password;

    if (!( username = $('#username').val() )) {
        return $('#username').wrong();
    }

    if (!( password = $('#password').val() )) {
        return $('#password').wrong('请输入您的登录密码');
    }

    $("#login_form").ajax(function(ret) {
    });

    return false;
}



$(function() {
    $('#reg_form').human();
    $('#login_form').human();

    $('#btn_nav_login').on('click', function() {
        $("#login_dialog").pop();
    });

    $('#login_submit').on('click', s_login_submit);


    $('#btn_nav_reg').on('click', function() {
        $("#reg_dialog").pop();
    });
    $('#reg_submit').on('click', s_reg_submit);

    //检查用户账号及用户昵称
    $('#rusername').on('blur', s_reg_has);
    $('#rnickname').on('blur', s_reg_has);

});
