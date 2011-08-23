<?php
/**
 * 提供用户注册的程序
 *
 *  /db/devdb.user.php
 *
 * */

require_once(__DIR__ . "/devinc.all.php");
require_once(ROOT_DIR . "/db/devdb.user.php");
require_once(ROOT_DIR . "/user/devapi.user.php");


$arg	= array();

if (a_bad_string($_GET["u"], $resetname)) {
    //没有输入用户名
    exit(a_action_msg("请输入您要找加的账号"));
}

$binded = isset($_GET["b"]) ? true : false;

// 取值完毕



//找回密码有两种：
//  1、通过用户名找回
//  2、通过绑定的手机号码或邮箱地址找回
if ($binded === false) {
    //通过用户名找回
    if (!( $user = a_user_by_username($resetname) )
	|| !( $secure = a_user_secure_by_resetname($username))

	|| a_bad_string($secure["resetname"], $resetname)
    ) {
	exit(a_action_msg("对不起，此账号还没有注册。"));
    }

} else if (!( $secure = a_user_secure_by_resetname($resetname) )
    //通过绑定手机号找回
	|| !( $user = a_db("user", $secure["uid"]) )
    ) {
	//没有对应的手机号码或邮箱地址
	exit(a_action_msg("对不起，此手机号码或邮件地址并没有被绑定过，请重新输入"));
    }


$url = "http://aiyuji.com/reset.php?u=";
$ret = false;


//判断用户名是否为手机号码
if (!f_bad_mobile($resetname)) {
    //直接往邮箱发送重置链接
    if (false !== ( $ret = a_sms_send($resetname, "您在爱语记上重量您的密码，请点击此处重新设置：{$url}") )) {
	//取手机号码的号码段和最后三位数字，其它位为*
	$prev	    = substr($resetname, 0, 3);
	$post	    = substr($resetname, -3);
	$replace    = substr($resetname, 3, -3);
	$replace    = str_replace(str_split($replace),'*', $replace);

	$replace  = $prev . $replace . $post;

	$ret = "重置信息已经通过短信发送到您的手机上（{$replace}），请注意查收，谢谢。";
    }

} else if (!f_bad_email($resetname)) {
    //直接往邮箱发送重置链接
    $ret = a_email_send(array(
	"email"	    => $resetname,
	"title"	    => "这是一封您在爱语记上要求重置密码的信件",
	"content"   => "亲爱的用户{$user['username']}您好，感谢您使用我们提供的密码找回服务。请点击{$url}链接重置您的登录密码。<br />在找回密码的过程中给您造成的不便我们感到非常抱歉，希望您能提供建议让我们为您服务得更好，谢谢。祝您身体健康，万事如意。 --- 爱语记",
    ));

    if ($ret !== false) {
	//取邮箱名称的前一位和后一们，其它位为*
	$token	    = substr($resetname, 0, strpos($resetname, '@'));

	//防止出现a@a.com邮件地址出错
	if (count($token) > 1) {
	    $prev	= substr($token, 0, 1);
	    $post	= substr($token, -1);
	    $replace	= substr($token, 1, -1);

	    if ($replace) {
		$replace = str_replace(str_split($replace),'*', $replace);
	    }

	    $replace	= $prev . $replace . $post . substr($resetname, strpos($resetname, '@'));

	} else {
	    //出现了a@qq.com的邮箱地址
	    $replace	= $resetname;
	}

	$ret = "重置链接已通过邮件发送到您的邮箱（{$replace}）中，请注意查收，谢谢。";
    }
}


if ($ret === false) {
    //发送失败
    exit(a_action_msg("对不起，发送重置信息失败，请稍后再试"));
}

//密码和用户正确
$arg["err"] = 0;

exit(a_action_msg($ret, "/login"));
