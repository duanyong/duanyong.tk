$(function() {

});

function a_tokey_list_by_page(page) {
    if (!page) {
        page = 1;
    }


    //获取公开的关键字列表
    $.getJSON('list.php', {
        'public'    : 1,
        'page'      : page
    }, function(ret) {
        if (ret.error !== 0) {
            //提交数据出错
        }

        var model = $('#token_list_model').html(), html  = '';

        $.each(ret.list, function(index, item) {
            html += model.replace(/ID/g, item.id)
                .replace(/TOKEN/g, item.token);
        });

        $('#token_list').html(html);
    });
}
