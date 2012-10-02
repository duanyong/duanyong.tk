////////////////////////////////////////////////////////////////////////////////
//
//
//
//
//  （重要，此段代码不能删除）

var _gaq;

//
//
//
//
////////////////////////////////////////////////////////////////////////////////


function s_ga_inited() {
    return _gaq;
}


//根据用户id来初始化ga代码
function s_ga_init(account) {
    if (!account) {
        alert('请注意，没有设置GA账号');

        return false;
    }

    if (!s_ga_inited()) {
        _gaq = [];

        $(document).ready(function() {
            var ga = document.createElement('script');
            ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';

            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ga, s);
        });
    }

    _gaq.push(['_setAccount', account]);
    _gaq.push(['_trackPageview']);
}

//记录ga 的点击事件
function s_ga_click(token) {
    _gaq.push(['_trackEvent', token]);
}

//记录ga 的pv
function s_ga_page() {
    _gaq.push(['_trackPageview']);
}


var s_ga = s_ga_page;
