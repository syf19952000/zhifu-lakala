<?php
session_start();
header("Content-type: text/html; charset=utf-8");
require_once("../inc/function.php");
require_once("../inc/pd.php");

if(is_weixin() == false){
    $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]; 

    echo "请用微信浏览器打开：".$url;exit;
}
require_once('weixin.class.php');
$uid = $_SESSION['uid'];
$weixin = new class_weixin();

if (!isset($_GET["code"])) {
    $redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $jumpurl = $weixin->oauth2_authorize($redirect_url, "snsapi_userinfo", "123");
    Header("Location: $jumpurl");exit;
} else {
    $access_token_oauth2 = $weixin->oauth2_access_token($_GET["code"]);
    $userinfo = $weixin->oauth2_get_user_info($access_token_oauth2['access_token'], $access_token_oauth2['openid']);
	$openid = $userinfo["openid"];
	$headimgurl = $userinfo["headimgurl"];
	$nickname = $userinfo["nickname"];
	$sex = $userinfo["sex"];
require_once("../inc/db.class.php");
	$sql = "update users set openid='".$openid."',headimgurl='".$headimgurl."',nickname='".$nickname."',sex=".$sex." where id=".$uid;
	if($rs = $connect->query($sql)){
		
	}else{
			echo "<script>alert('系统出错，请重试！');window.location.href='../wap/gl.php';</script>";
			exit();
	}

	$url = '../wap/ziliao.php';
	//结束
	Header("Location: $url");exit();
}
exit();
?>