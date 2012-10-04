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
    var ret = true;

    if (!$('#username').val()) {
        ret = $('#username').wrong('您可以用邮箱或者手机号来登录');
    }

    if (!$('#password').val()) {
        ret = $('#password').wrong('请输入5位以上的密码');
    }

    if (!$('#nickname').val()) {
        ret = $('#nickname').wrong();
    }

    return ret;
}

$(function() {
    $('#reg_submit').on('click', s_reg_check);

    //检查用户账号及用户昵称
    $('#username').on('blur', s_reg_has);
    $('#nickname').on('blur', s_reg_has);
});
