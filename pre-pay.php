<?php
header("content-type:text/html;charset=utf-8");
require_once("const.php");
require_once("inc/db.class.php");

require("lakala.inc.php");   // 引入类文件

// $wechat=new lakala(APPID,APPSECRET,TOKEN);
// echo $wechat->_getTicket();


$money = isset($_GET['money']) ? $_GET['money'] : $money;
$channel = ($_GET['channel'] ?? '');
$userip = get_client_ip();     //获得用户设备 IP

$domain = $mch['data']['mch_domain'];

// $out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : date('YmdHis').rand(1000,9999);//平台内部订单号

$notify_url = "https://" . $mch['data']['mch_domain'] . '/notify' . $channel . '.php';

$total = check_third_order($connect);

if($total < 3) {
    // $Md5key = "827s%s3sj7sjk2!@ks";   //密钥
    // $post_data = array(
    //     "amount" => $money,
    //     'appid' => '1304',
    //     "clientIp" => $userip,
    //     'billno' => $out_trade_no,
    //     "notifyUrl" => $notify_url,
    //     "returnUrl" => "https://" . $domain . "/wancheng.html",
    //     "payType" => 1
    // );

    // $md5str = "";
    // foreach ($post_data as $key => $val) {
    //     $md5str = $md5str . $val;
    // }

    // $sign = md5($md5str . $Md5key);

    // $post_data["signature"] = $sign;
    // $post_data["body"] = "精彩短剧解锁客服电话01053396307";

    // $response = postUrl('https://a.hm0721.com/Api/Pay/Order', $post_data);
    // $response = json_decode($response, true);

    // if($response['state'] == '0'){
    //     echo $response['url'];
    // }else{
    //     echo 'error: ' . $response['msg'];
    // }


var_dump('打印回调地址');
var_dump($notify_url);

 // var_dump($money);  var_dump((string)($money * 10)); die;

        $out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : date('YmdHis').rand(1000,9999);//平台内部订单号
        // $cert = 'D:/www/development/0619/guanmo.koare.com/public/lakala' . '/OP00000003_private_key.pem';

        $pay = new lakala();

        $pay->setAppid($lakala_appid);
        $pay->setMchSerialNo('00dfba8194c41b84cf');  // 证书序列号
        $pay->setOutOrderNo($out_trade_no);
        $pay->setMercId($lakala_merchant_no);
        $pay->setAmount((string)($money * 10));   // 单位 分
        $pay->setNotifyUrl($notify_url);
        $pay->setSubject('精彩短剧解锁客服电话01053396307');
        $pay->setMerchantPrivateKeyPath($lakala_merchantPrivateKeyPath);

        $res = $pay->pay();  // 发起支付

        var_dump($res);die;
        // 什么时候入 order表

        // if($response['sysRetCode'] == '000000'){
        //     echo urldecode($response['bizData']['redirectUrl']);
        // }else{
        //     echo 'error: ' . $response['bizData']['tradeRetMsg'];
        // }

        if($response['state'] == '0'){
            echo $response['url'];
        }else{
            echo 'error: ' . $response['msg'];
        }


}
