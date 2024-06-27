<?php
session_start();
header("Content-type: text/html; charset=utf-8");
require_once("../inc/function.php");

if(is_weixin() == false){
    $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]; 

    echo "请用微信浏览器打开：".$url;exit;
}
require_once('weixin.class.php');

$weixin = new class_weixin();

if (!isset($_GET["code"])) {
    $redirect_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $jumpurl = $weixin->oauth2_authorize($redirect_url, "snsapi_userinfo", "234");
    Header("Location: $jumpurl");exit;
} else {
    $access_token_oauth2 = $weixin->oauth2_access_token($_GET["code"]);
    $userinfo = $weixin->oauth2_get_user_info($access_token_oauth2['access_token'], $access_token_oauth2['openid']);

	$openid = $userinfo["openid"];
	$nickname = $userinfo["nickname"];
	$sex = $userinfo["sex"];
	$headimgurl = $userinfo["headimgurl"];
	$time = time();
	//var_dump($userinfo);exit;
require_once("../inc/db.class.php");

	$sql = "select * from weixin where openid='".$openid."'";
	if($rs = $connect->once($sql)){
		$_SESSION['uid'] = $rs['id'];
		
	}else{
		$sql = "INSERT INTO weixin (openid, nickname, sex, headimgurl, zctime) values ('$openid', '$nickname', $sex, '$headimgurl', $time)";
		$connect->query($sql);
		$_SESSION['uid'] = $rs['id'];
		$_SESSION['zt'] = 1;
	}

	//结束
	$url = '../zp/';
	Header("Location: $url");exit();
}
?>