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
    $('#login_form').human();
    $('#login_submit').on('click', s_login_check);
});

