<?php
define("APPID","wxd9050b5faaa70928");
define("APPSECRET","11a7494193a475f8261f9ab97e4a9144");
define("TOKEN","weixin");
require("wechat.inc.php");
//header("content-type:image/jpg");
$wechat=new WeChat(APPID,APPSECRET,TOKEN);
echo $wechat->_getTicket();
?>