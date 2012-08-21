$(function() {

//提交到服务器
$('#words_form').submit(function() {
    //检查表单是否正在提交
    if ($('#words_form').attr('locked')) {
        //防止双击提交
        return false;
    }

    //锁定表单
    $('#words_form').attr('locked', true);


    //进度显示
    $("#words_indicator").ajaxStart(function() {
        $(this).html("<img src='img/ajax_wait.gif' />").fadeIn(400);
    });

    $.post('words.php', $('#words_form').serialize(), function(ret) {
        if (ret.error !== 0) {
            //提交数据出错
        }

        //解除锁定
        $('#words_form').attr('locked', '');

        $("#words_indicator").ajaxStop(function() {
            $(this).html("<img src='img/ajax_ok.jpg' />").delay(800).fadeOut(400);

        });
    }, 'json');


    return false;
});

});
