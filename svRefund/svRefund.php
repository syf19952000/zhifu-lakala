<?php
// 基础信息
require("../const.php");
require_once("../inc/db.class.php");
require("lakala.inc.php");   // 引入类文件

// require_once ("../Yop-SDK/YopRsaClient.php");
// require_once ("../Yop-SDK/YopClient3.php");
// require_once ("../Yop-SDK/Util/YopSignUtils.php");

header('Content-type:text/html; Charset=utf-8');
$domain = $mch['data']['mch_domain'];

function getOrder($orderId){   // 查询订单支付状态

    $out_trade_no = $orderId;
    // $cert = 'svRefund/cert/' . $mch['mch_id'] . '/apiclient_cert.pem';
    // $cert = 'D:/www/development/0619/guanmo.koare.com/public/lakala' . '/OP00000003_private_key.pem';

    $pay = new LaKaPayLibrary();

    $pay->setOutOrderNo($out_trade_no);
    $pay->setMercId($lakala_merchant_no);
    $pay->setMerchantPrivateKeyPath($lakala_merchantPrivateKeyPath);
    $pay->setAppid($lakala_appid);

    $response = $pay->tradeQuery();


    // $response = postUrl('https://mtest.eycard.cn/cashier/trade/query', $post_data);

    // $response = json_decode($response, true);

    if($response['sysRetCode'] == '000000'){
        return true;
    }else{
        return false;
    }
}

// 如果不为空
if (isset($_GET['paynum'])) {
    $refundNo = date('YmdHis') . rand(1000, 9999);//退款订单号平台内部订单号
    $result = '';
    $wxOrderNo = $_GET['paynum']; // 订单号
    $sql = "select * from `order` where nonce_str like '%" . $wxOrderNo . "%' ";
    $result = $connect->query($sql);
    $result = mysqli_fetch_array($result);

    if($result){
        $transaction_id = $result['transaction_id'];
        $out_trade_no = $result['out_trade_no'];
        $res = getOrder($out_trade_no);   // 如果订单已经成功
        if($res){
            $app_id = $result['mch_id'];
            $money = $result['money'] * 100;
            $notify_url = "https://" . $domain . "/svRefund/notify.php";


            // $request = new YopRequest($config['app_key'], $config['isv_private_key'][0]['value']);
            // //加入请求参数，参考手册
            // $request->addParam("parentMerchantNo", $merchantNo);                                            //发起方商户编号
            // $request->addParam("merchantNo", $merchantNo);              //收款商户编号
            // $request->addParam("refundRequestId", $refundNo);                           //订单号
            // $request->addParam("orderId", $out_trade_no);                           //订单号
            // $request->addParam("refundAmount", $money);                     //订单金额
            // $request->addParam("description", "用户申请退款");                        //商品信息
            // $request->addParam("notifyUrl", $notify_url);                           //回调通知地址

            // //提交Post请求，第一个参数为手册上的接口地址
            // $response = json_decode(json_encode(YopRsaClient::post("/rest/v1.0/trade/refund", $request)), true);



            // $out_trade_no = '202406251126009262';

            // $cert = 'svRefund/cert/' . $mch['mch_id'] . '/apiclient_cert.pem';
            // $cert = 'D:/www/development/0619/guanmo.koare.com/public/lakala' . '/OP00000003_private_key.pem';

            $pay = new LaKaPayLibrary();

            $pay->setOutOrderNo($out_trade_no);
            $pay->setMercId($lakala_merchant_no);
            $pay->setMerchantPrivateKeyPath($lakala_merchantPrivateKeyPath);
            $pay->setAppid($lakala_appid);
            $pay->setAmount($money);
            $pay->setNotifyUrl($notify_url);  // 退款回调地址

            $response = $pay->tradeRefund();

            // $response = json_decode($response, true);

var_dump('退款');
var_dump($response);
die;
            $result = 'false';
            if($response['result']['code'] == 'OPR00000'){
                $sql = "UPDATE `order` set refund = 1 where transaction_id = '$wxOrderNo' ";
                $connect->query($sql);

                $sql = "INSERT INTO `refund` (transaction_id, out_trade_no, out_refund_no, mch_id, refund_fee, total_fee, status) VALUES ('$transaction_id', '$out_trade_no', '$refundNo', '$app_id', '$money', '$money', 1)";
                $connect->query($sql);
                $result = 'true';
            }
        }
    }
    
}

?>
<!DOCTYPE html>
<!-- saved from url=https://h61.amkj.store/pages/refund/svRefund -->
<html lang="zh-CN"
      style="--status-bar-height: 0px; --top-window-height: 0px; --window-left: 0px; --window-right: 0px; --window-margin: 0px; --window-top: calc(var(--top-window-height) + 0px); --window-bottom: 0px;">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <script>var coverSupport = 'CSS' in window && typeof CSS.supports === 'function' && (CSS.supports('top: env(a)') || CSS.supports('top: constant(a)'))
        document.write('<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0' + (coverSupport ? ', viewport-fit=cover' : '') + '" />')</script>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, viewport-fit=cover">
    <link rel="stylesheet" href="./h61.amkj.store_pages_refund_svRefund_files/index.63b34199.css">
    <style type="text/css">
        @charset "UTF-8";

        /* 水平间距 */
        /* 水平间距 */
        /*每个页面公共css */
        /* 水平间距 */
        @font-face {
            font-family: customicons;
            /* Project id 2878519 */
            src: url(/static/customicons.ttf) format("truetype")
        }

        .customicons {
            font-family: customicons !important
        }

        .youxi:before {
            content: "\e60e"
        }

        .wenjian:before {
            content: "\e60f"
        }

        .zhuanfa:before {
            content: "\e610"
        }

        .uni-border {
            border: 1px #f0f0f0 solid
        }

        .uni-primary {
            color: #2979ff
        }

        .uni-primary-bg {
            background-color: #2979ff
        }

        .uni-primary-disable {
            color: #94bcff
        }

        .uni-primary-disable-bg {
            background-color: #94bcff
        }

        .uni-primary-light {
            color: #d4e4ff
        }

        .uni-primary-light-bg {
            background-color: #d4e4ff
        }

        .uni-success {
            color: #18bc37
        }

        .uni-success-bg {
            background-color: #18bc37
        }

        .uni-success-disable {
            color: #8cde9b
        }

        .uni-success-disable-bg {
            background-color: #8cde9b
        }

        .uni-success-light {
            color: #d1f2d7
        }

        .uni-success-light-bg {
            background-color: #d1f2d7
        }

        .uni-warning {
            color: #f3a73f
        }

        .uni-warning-bg {
            background-color: #f3a73f
        }

        .uni-warning-disable {
            color: #f9d39f
        }

        .uni-warning-disable-bg {
            background-color: #f9d39f
        }

        .uni-warning-light {
            color: #fdedd9
        }

        .uni-warning-light-bg {
            background-color: #fdedd9
        }

        .uni-error {
            color: #e43d33
        }

        .uni-error-bg {
            background-color: #e43d33
        }

        .uni-error-disable {
            color: #f29e99
        }

        .uni-error-disable-bg {
            background-color: #f29e99
        }

        .uni-error-light {
            color: #fad8d6
        }

        .uni-error-light-bg {
            background-color: #fad8d6
        }

        .uni-info {
            color: #8f939c
        }

        .uni-info-bg {
            background-color: #8f939c
        }

        .uni-info-disable {
            color: #c7c9ce
        }

        .uni-info-disable-bg {
            background-color: #c7c9ce
        }

        .uni-info-light {
            color: #e9e9eb
        }

        .uni-info-light-bg {
            background-color: #e9e9eb
        }

        .uni-main-color {
            color: #3a3a3a
        }

        .uni-main-color-bg {
            background-color: #3a3a3a
        }

        .uni-base-color {
            color: #6a6a6a
        }

        .uni-base-color-bg {
            background-color: #6a6a6a
        }

        .uni-secondary-color {
            color: #909399
        }

        .uni-secondary-color-bg {
            background-color: #909399
        }

        .uni-extra-color {
            color: #c7c7c7
        }

        .uni-extra-color-bg {
            background-color: #c7c7c7
        }

        .uni-bg-color {
            color: #f7f7f7
        }

        .uni-bg-color-bg {
            background-color: #f7f7f7
        }

        .uni-border-1 {
            color: #f0f0f0
        }

        .uni-border-1-bg {
            background-color: #f0f0f0
        }

        .uni-border-2 {
            color: #ededed
        }

        .uni-border-2-bg {
            background-color: #ededed
        }

        .uni-border-3 {
            color: #dcdcdc
        }

        .uni-border-3-bg {
            background-color: #dcdcdc
        }

        .uni-border-4 {
            color: #b9b9b9
        }

        .uni-border-4-bg {
            background-color: #b9b9b9
        }

        .uni-black {
            color: #000
        }

        .uni-black-bg {
            background-color: #000
        }

        .uni-white {
            color: #fff
        }

        .uni-white-bg {
            background-color: #fff
        }

        .uni-transparent {
            color: transparent
        }

        .uni-transparent-bg {
            background-color: transparent
        }

        .uni-shadow-sm {
            box-shadow: 0 0 5px hsla(0, 0%, 84.7%, .5)
        }

        .uni-shadow-base {
            box-shadow: 0 1px 8px 1px hsla(0, 0%, 64.7%, .2)
        }

        .uni-shadow-lg {
            box-shadow: 0 1px 10px 2px rgba(165, 164, 164, .5)
        }

        .uni-mask {
            background-color: rgba(0, 0, 0, .4)
        }

        .uni-mt-0 {
            margin-top: 0
        }

        .uni-mt-n0 {
            margin-top: 0
        }

        .uni-mr-0 {
            margin-right: 0
        }

        .uni-mr-n0 {
            margin-right: 0
        }

        .uni-mb-0 {
            margin-bottom: 0
        }

        .uni-mb-n0 {
            margin-bottom: 0
        }

        .uni-ml-0 {
            margin-left: 0
        }

        .uni-ml-n0 {
            margin-left: 0
        }

        .uni-mx-0 {
            margin-left: 0;
            margin-right: 0
        }

        .uni-mx-n0 {
            margin-left: 0;
            margin-right: 0
        }

        .uni-my-0 {
            margin-top: 0;
            margin-bottom: 0
        }

        .uni-my-n0 {
            margin-top: 0;
            margin-bottom: 0
        }

        .uni-ma-0 {
            margin: 0
        }

        .uni-ma-n0 {
            margin: 0
        }

        .uni-mt-1 {
            margin-top: 2px
        }

        .uni-mt-n1 {
            margin-top: -2px
        }

        .uni-mr-1 {
            margin-right: 2px
        }

        .uni-mr-n1 {
            margin-right: -2px
        }

        .uni-mb-1 {
            margin-bottom: 2px
        }

        .uni-mb-n1 {
            margin-bottom: -2px
        }

        .uni-ml-1 {
            margin-left: 2px
        }

        .uni-ml-n1 {
            margin-left: -2px
        }

        .uni-mx-1 {
            margin-left: 2px;
            margin-right: 2px
        }

        .uni-mx-n1 {
            margin-left: -2px;
            margin-right: -2px
        }

        .uni-my-1 {
            margin-top: 2px;
            margin-bottom: 2px
        }

        .uni-my-n1 {
            margin-top: -2px;
            margin-bottom: -2px
        }

        .uni-ma-1 {
            margin: 2px
        }

        .uni-ma-n1 {
            margin: -2px
        }

        .uni-mt-2 {
            margin-top: 4px
        }

        .uni-mt-n2 {
            margin-top: -4px
        }

        .uni-mr-2 {
            margin-right: 4px
        }

        .uni-mr-n2 {
            margin-right: -4px
        }

        .uni-mb-2 {
            margin-bottom: 4px
        }

        .uni-mb-n2 {
            margin-bottom: -4px
        }

        .uni-ml-2 {
            margin-left: 4px
        }

        .uni-ml-n2 {
            margin-left: -4px
        }

        .uni-mx-2 {
            margin-left: 4px;
            margin-right: 4px
        }

        .uni-mx-n2 {
            margin-left: -4px;
            margin-right: -4px
        }

        .uni-my-2 {
            margin-top: 4px;
            margin-bottom: 4px
        }

        .uni-my-n2 {
            margin-top: -4px;
            margin-bottom: -4px
        }

        .uni-ma-2 {
            margin: 4px
        }

        .uni-ma-n2 {
            margin: -4px
        }

        .uni-mt-3 {
            margin-top: 6px
        }

        .uni-mt-n3 {
            margin-top: -6px
        }

        .uni-mr-3 {
            margin-right: 6px
        }

        .uni-mr-n3 {
            margin-right: -6px
        }

        .uni-mb-3 {
            margin-bottom: 6px
        }

        .uni-mb-n3 {
            margin-bottom: -6px
        }

        .uni-ml-3 {
            margin-left: 6px
        }

        .uni-ml-n3 {
            margin-left: -6px
        }

        .uni-mx-3 {
            margin-left: 6px;
            margin-right: 6px
        }

        .uni-mx-n3 {
            margin-left: -6px;
            margin-right: -6px
        }

        .uni-my-3 {
            margin-top: 6px;
            margin-bottom: 6px
        }

        .uni-my-n3 {
            margin-top: -6px;
            margin-bottom: -6px
        }

        .uni-ma-3 {
            margin: 6px
        }

        .uni-ma-n3 {
            margin: -6px
        }

        .uni-mt-4 {
            margin-top: 8px
        }

        .uni-mt-n4 {
            margin-top: -8px
        }

        .uni-mr-4 {
            margin-right: 8px
        }

        .uni-mr-n4 {
            margin-right: -8px
        }

        .uni-mb-4 {
            margin-bottom: 8px
        }

        .uni-mb-n4 {
            margin-bottom: -8px
        }

        .uni-ml-4 {
            margin-left: 8px
        }

        .uni-ml-n4 {
            margin-left: -8px
        }

        .uni-mx-4 {
            margin-left: 8px;
            margin-right: 8px
        }

        .uni-mx-n4 {
            margin-left: -8px;
            margin-right: -8px
        }

        .uni-my-4 {
            margin-top: 8px;
            margin-bottom: 8px
        }

        .uni-my-n4 {
            margin-top: -8px;
            margin-bottom: -8px
        }

        .uni-ma-4 {
            margin: 8px
        }

        .uni-ma-n4 {
            margin: -8px
        }

        .uni-mt-5 {
            margin-top: 10px
        }

        .uni-mt-n5 {
            margin-top: -10px
        }

        .uni-mr-5 {
            margin-right: 10px
        }

        .uni-mr-n5 {
            margin-right: -10px
        }

        .uni-mb-5 {
            margin-bottom: 10px
        }

        .uni-mb-n5 {
            margin-bottom: -10px
        }

        .uni-ml-5 {
            margin-left: 10px
        }

        .uni-ml-n5 {
            margin-left: -10px
        }

        .uni-mx-5 {
            margin-left: 10px;
            margin-right: 10px
        }

        .uni-mx-n5 {
            margin-left: -10px;
            margin-right: -10px
        }

        .uni-my-5 {
            margin-top: 10px;
            margin-bottom: 10px
        }

        .uni-my-n5 {
            margin-top: -10px;
            margin-bottom: -10px
        }

        .uni-ma-5 {
            margin: 10px
        }

        .uni-ma-n5 {
            margin: -10px
        }

        .uni-mt-6 {
            margin-top: 12px
        }

        .uni-mt-n6 {
            margin-top: -12px
        }

        .uni-mr-6 {
            margin-right: 12px
        }

        .uni-mr-n6 {
            margin-right: -12px
        }

        .uni-mb-6 {
            margin-bottom: 12px
        }

        .uni-mb-n6 {
            margin-bottom: -12px
        }

        .uni-ml-6 {
            margin-left: 12px
        }

        .uni-ml-n6 {
            margin-left: -12px
        }

        .uni-mx-6 {
            margin-left: 12px;
            margin-right: 12px
        }

        .uni-mx-n6 {
            margin-left: -12px;
            margin-right: -12px
        }

        .uni-my-6 {
            margin-top: 12px;
            margin-bottom: 12px
        }

        .uni-my-n6 {
            margin-top: -12px;
            margin-bottom: -12px
        }

        .uni-ma-6 {
            margin: 12px
        }

        .uni-ma-n6 {
            margin: -12px
        }

        .uni-mt-7 {
            margin-top: 14px
        }

        .uni-mt-n7 {
            margin-top: -14px
        }

        .uni-mr-7 {
            margin-right: 14px
        }

        .uni-mr-n7 {
            margin-right: -14px
        }

        .uni-mb-7 {
            margin-bottom: 14px
        }

        .uni-mb-n7 {
            margin-bottom: -14px
        }

        .uni-ml-7 {
            margin-left: 14px
        }

        .uni-ml-n7 {
            margin-left: -14px
        }

        .uni-mx-7 {
            margin-left: 14px;
            margin-right: 14px
        }

        .uni-mx-n7 {
            margin-left: -14px;
            margin-right: -14px
        }

        .uni-my-7 {
            margin-top: 14px;
            margin-bottom: 14px
        }

        .uni-my-n7 {
            margin-top: -14px;
            margin-bottom: -14px
        }

        .uni-ma-7 {
            margin: 14px
        }

        .uni-ma-n7 {
            margin: -14px
        }

        .uni-mt-8 {
            margin-top: 16px
        }

        .uni-mt-n8 {
            margin-top: -16px
        }

        .uni-mr-8 {
            margin-right: 16px
        }

        .uni-mr-n8 {
            margin-right: -16px
        }

        .uni-mb-8 {
            margin-bottom: 16px
        }

        .uni-mb-n8 {
            margin-bottom: -16px
        }

        .uni-ml-8 {
            margin-left: 16px
        }

        .uni-ml-n8 {
            margin-left: -16px
        }

        .uni-mx-8 {
            margin-left: 16px;
            margin-right: 16px
        }

        .uni-mx-n8 {
            margin-left: -16px;
            margin-right: -16px
        }

        .uni-my-8 {
            margin-top: 16px;
            margin-bottom: 16px
        }

        .uni-my-n8 {
            margin-top: -16px;
            margin-bottom: -16px
        }

        .uni-ma-8 {
            margin: 16px
        }

        .uni-ma-n8 {
            margin: -16px
        }

        .uni-mt-9 {
            margin-top: 18px
        }

        .uni-mt-n9 {
            margin-top: -18px
        }

        .uni-mr-9 {
            margin-right: 18px
        }

        .uni-mr-n9 {
            margin-right: -18px
        }

        .uni-mb-9 {
            margin-bottom: 18px
        }

        .uni-mb-n9 {
            margin-bottom: -18px
        }

        .uni-ml-9 {
            margin-left: 18px
        }

        .uni-ml-n9 {
            margin-left: -18px
        }

        .uni-mx-9 {
            margin-left: 18px;
            margin-right: 18px
        }

        .uni-mx-n9 {
            margin-left: -18px;
            margin-right: -18px
        }

        .uni-my-9 {
            margin-top: 18px;
            margin-bottom: 18px
        }

        .uni-my-n9 {
            margin-top: -18px;
            margin-bottom: -18px
        }

        .uni-ma-9 {
            margin: 18px
        }

        .uni-ma-n9 {
            margin: -18px
        }

        .uni-mt-10 {
            margin-top: 20px
        }

        .uni-mt-n10 {
            margin-top: -20px
        }

        .uni-mr-10 {
            margin-right: 20px
        }

        .uni-mr-n10 {
            margin-right: -20px
        }

        .uni-mb-10 {
            margin-bottom: 20px
        }

        .uni-mb-n10 {
            margin-bottom: -20px
        }

        .uni-ml-10 {
            margin-left: 20px
        }

        .uni-ml-n10 {
            margin-left: -20px
        }

        .uni-mx-10 {
            margin-left: 20px;
            margin-right: 20px
        }

        .uni-mx-n10 {
            margin-left: -20px;
            margin-right: -20px
        }

        .uni-my-10 {
            margin-top: 20px;
            margin-bottom: 20px
        }

        .uni-my-n10 {
            margin-top: -20px;
            margin-bottom: -20px
        }

        .uni-ma-10 {
            margin: 20px
        }

        .uni-ma-n10 {
            margin: -20px
        }

        .uni-mt-11 {
            margin-top: 22px
        }

        .uni-mt-n11 {
            margin-top: -22px
        }

        .uni-mr-11 {
            margin-right: 22px
        }

        .uni-mr-n11 {
            margin-right: -22px
        }

        .uni-mb-11 {
            margin-bottom: 22px
        }

        .uni-mb-n11 {
            margin-bottom: -22px
        }

        .uni-ml-11 {
            margin-left: 22px
        }

        .uni-ml-n11 {
            margin-left: -22px
        }

        .uni-mx-11 {
            margin-left: 22px;
            margin-right: 22px
        }

        .uni-mx-n11 {
            margin-left: -22px;
            margin-right: -22px
        }

        .uni-my-11 {
            margin-top: 22px;
            margin-bottom: 22px
        }

        .uni-my-n11 {
            margin-top: -22px;
            margin-bottom: -22px
        }

        .uni-ma-11 {
            margin: 22px
        }

        .uni-ma-n11 {
            margin: -22px
        }

        .uni-mt-12 {
            margin-top: 24px
        }

        .uni-mt-n12 {
            margin-top: -24px
        }

        .uni-mr-12 {
            margin-right: 24px
        }

        .uni-mr-n12 {
            margin-right: -24px
        }

        .uni-mb-12 {
            margin-bottom: 24px
        }

        .uni-mb-n12 {
            margin-bottom: -24px
        }

        .uni-ml-12 {
            margin-left: 24px
        }

        .uni-ml-n12 {
            margin-left: -24px
        }

        .uni-mx-12 {
            margin-left: 24px;
            margin-right: 24px
        }

        .uni-mx-n12 {
            margin-left: -24px;
            margin-right: -24px
        }

        .uni-my-12 {
            margin-top: 24px;
            margin-bottom: 24px
        }

        .uni-my-n12 {
            margin-top: -24px;
            margin-bottom: -24px
        }

        .uni-ma-12 {
            margin: 24px
        }

        .uni-ma-n12 {
            margin: -24px
        }

        .uni-mt-13 {
            margin-top: 26px
        }

        .uni-mt-n13 {
            margin-top: -26px
        }

        .uni-mr-13 {
            margin-right: 26px
        }

        .uni-mr-n13 {
            margin-right: -26px
        }

        .uni-mb-13 {
            margin-bottom: 26px
        }

        .uni-mb-n13 {
            margin-bottom: -26px
        }

        .uni-ml-13 {
            margin-left: 26px
        }

        .uni-ml-n13 {
            margin-left: -26px
        }

        .uni-mx-13 {
            margin-left: 26px;
            margin-right: 26px
        }

        .uni-mx-n13 {
            margin-left: -26px;
            margin-right: -26px
        }

        .uni-my-13 {
            margin-top: 26px;
            margin-bottom: 26px
        }

        .uni-my-n13 {
            margin-top: -26px;
            margin-bottom: -26px
        }

        .uni-ma-13 {
            margin: 26px
        }

        .uni-ma-n13 {
            margin: -26px
        }

        .uni-mt-14 {
            margin-top: 28px
        }

        .uni-mt-n14 {
            margin-top: -28px
        }

        .uni-mr-14 {
            margin-right: 28px
        }

        .uni-mr-n14 {
            margin-right: -28px
        }

        .uni-mb-14 {
            margin-bottom: 28px
        }

        .uni-mb-n14 {
            margin-bottom: -28px
        }

        .uni-ml-14 {
            margin-left: 28px
        }

        .uni-ml-n14 {
            margin-left: -28px
        }

        .uni-mx-14 {
            margin-left: 28px;
            margin-right: 28px
        }

        .uni-mx-n14 {
            margin-left: -28px;
            margin-right: -28px
        }

        .uni-my-14 {
            margin-top: 28px;
            margin-bottom: 28px
        }

        .uni-my-n14 {
            margin-top: -28px;
            margin-bottom: -28px
        }

        .uni-ma-14 {
            margin: 28px
        }

        .uni-ma-n14 {
            margin: -28px
        }

        .uni-mt-15 {
            margin-top: 30px
        }

        .uni-mt-n15 {
            margin-top: -30px
        }

        .uni-mr-15 {
            margin-right: 30px
        }

        .uni-mr-n15 {
            margin-right: -30px
        }

        .uni-mb-15 {
            margin-bottom: 30px
        }

        .uni-mb-n15 {
            margin-bottom: -30px
        }

        .uni-ml-15 {
            margin-left: 30px
        }

        .uni-ml-n15 {
            margin-left: -30px
        }

        .uni-mx-15 {
            margin-left: 30px;
            margin-right: 30px
        }

        .uni-mx-n15 {
            margin-left: -30px;
            margin-right: -30px
        }

        .uni-my-15 {
            margin-top: 30px;
            margin-bottom: 30px
        }

        .uni-my-n15 {
            margin-top: -30px;
            margin-bottom: -30px
        }

        .uni-ma-15 {
            margin: 30px
        }

        .uni-ma-n15 {
            margin: -30px
        }

        .uni-mt-16 {
            margin-top: 32px
        }

        .uni-mt-n16 {
            margin-top: -32px
        }

        .uni-mr-16 {
            margin-right: 32px
        }

        .uni-mr-n16 {
            margin-right: -32px
        }

        .uni-mb-16 {
            margin-bottom: 32px
        }

        .uni-mb-n16 {
            margin-bottom: -32px
        }

        .uni-ml-16 {
            margin-left: 32px
        }

        .uni-ml-n16 {
            margin-left: -32px
        }

        .uni-mx-16 {
            margin-left: 32px;
            margin-right: 32px
        }

        .uni-mx-n16 {
            margin-left: -32px;
            margin-right: -32px
        }

        .uni-my-16 {
            margin-top: 32px;
            margin-bottom: 32px
        }

        .uni-my-n16 {
            margin-top: -32px;
            margin-bottom: -32px
        }

        .uni-ma-16 {
            margin: 32px
        }

        .uni-ma-n16 {
            margin: -32px
        }

        .uni-pt-0 {
            padding-top: 0
        }

        .uni-pt-n0 {
            padding-top: 0
        }

        .uni-pr-0 {
            padding-right: 0
        }

        .uni-pr-n0 {
            padding-right: 0
        }

        .uni-pb-0 {
            padding-bottom: 0
        }

        .uni-pb-n0 {
            padding-bottom: 0
        }

        .uni-pl-0 {
            padding-left: 0
        }

        .uni-pl-n0 {
            padding-left: 0
        }

        .uni-px-0 {
            padding-left: 0;
            padding-right: 0
        }

        .uni-px-n0 {
            padding-left: 0;
            padding-right: 0
        }

        .uni-py-0 {
            padding-top: 0;
            padding-bottom: 0
        }

        .uni-py-n0 {
            padding-top: 0;
            padding-bottom: 0
        }

        .uni-pa-0 {
            padding: 0
        }

        .uni-pa-n0 {
            padding: 0
        }

        .uni-pt-1 {
            padding-top: 2px
        }

        .uni-pt-n1 {
            padding-top: -2px
        }

        .uni-pr-1 {
            padding-right: 2px
        }

        .uni-pr-n1 {
            padding-right: -2px
        }

        .uni-pb-1 {
            padding-bottom: 2px
        }

        .uni-pb-n1 {
            padding-bottom: -2px
        }

        .uni-pl-1 {
            padding-left: 2px
        }

        .uni-pl-n1 {
            padding-left: -2px
        }

        .uni-px-1 {
            padding-left: 2px;
            padding-right: 2px
        }

        .uni-px-n1 {
            padding-left: -2px;
            padding-right: -2px
        }

        .uni-py-1 {
            padding-top: 2px;
            padding-bottom: 2px
        }

        .uni-py-n1 {
            padding-top: -2px;
            padding-bottom: -2px
        }

        .uni-pa-1 {
            padding: 2px
        }

        .uni-pa-n1 {
            padding: -2px
        }

        .uni-pt-2 {
            padding-top: 4px
        }

        .uni-pt-n2 {
            padding-top: -4px
        }

        .uni-pr-2 {
            padding-right: 4px
        }

        .uni-pr-n2 {
            padding-right: -4px
        }

        .uni-pb-2 {
            padding-bottom: 4px
        }

        .uni-pb-n2 {
            padding-bottom: -4px
        }

        .uni-pl-2 {
            padding-left: 4px
        }

        .uni-pl-n2 {
            padding-left: -4px
        }

        .uni-px-2 {
            padding-left: 4px;
            padding-right: 4px
        }

        .uni-px-n2 {
            padding-left: -4px;
            padding-right: -4px
        }

        .uni-py-2 {
            padding-top: 4px;
            padding-bottom: 4px
        }

        .uni-py-n2 {
            padding-top: -4px;
            padding-bottom: -4px
        }

        .uni-pa-2 {
            padding: 4px
        }

        .uni-pa-n2 {
            padding: -4px
        }

        .uni-pt-3 {
            padding-top: 6px
        }

        .uni-pt-n3 {
            padding-top: -6px
        }

        .uni-pr-3 {
            padding-right: 6px
        }

        .uni-pr-n3 {
            padding-right: -6px
        }

        .uni-pb-3 {
            padding-bottom: 6px
        }

        .uni-pb-n3 {
            padding-bottom: -6px
        }

        .uni-pl-3 {
            padding-left: 6px
        }

        .uni-pl-n3 {
            padding-left: -6px
        }

        .uni-px-3 {
            padding-left: 6px;
            padding-right: 6px
        }

        .uni-px-n3 {
            padding-left: -6px;
            padding-right: -6px
        }

        .uni-py-3 {
            padding-top: 6px;
            padding-bottom: 6px
        }

        .uni-py-n3 {
            padding-top: -6px;
            padding-bottom: -6px
        }

        .uni-pa-3 {
            padding: 6px
        }

        .uni-pa-n3 {
            padding: -6px
        }

        .uni-pt-4 {
            padding-top: 8px
        }

        .uni-pt-n4 {
            padding-top: -8px
        }

        .uni-pr-4 {
            padding-right: 8px
        }

        .uni-pr-n4 {
            padding-right: -8px
        }

        .uni-pb-4 {
            padding-bottom: 8px
        }

        .uni-pb-n4 {
            padding-bottom: -8px
        }

        .uni-pl-4 {
            padding-left: 8px
        }

        .uni-pl-n4 {
            padding-left: -8px
        }

        .uni-px-4 {
            padding-left: 8px;
            padding-right: 8px
        }

        .uni-px-n4 {
            padding-left: -8px;
            padding-right: -8px
        }

        .uni-py-4 {
            padding-top: 8px;
            padding-bottom: 8px
        }

        .uni-py-n4 {
            padding-top: -8px;
            padding-bottom: -8px
        }

        .uni-pa-4 {
            padding: 8px
        }

        .uni-pa-n4 {
            padding: -8px
        }

        .uni-pt-5 {
            padding-top: 10px
        }

        .uni-pt-n5 {
            padding-top: -10px
        }

        .uni-pr-5 {
            padding-right: 10px
        }

        .uni-pr-n5 {
            padding-right: -10px
        }

        .uni-pb-5 {
            padding-bottom: 10px
        }

        .uni-pb-n5 {
            padding-bottom: -10px
        }

        .uni-pl-5 {
            padding-left: 10px
        }

        .uni-pl-n5 {
            padding-left: -10px
        }

        .uni-px-5 {
            padding-left: 10px;
            padding-right: 10px
        }

        .uni-px-n5 {
            padding-left: -10px;
            padding-right: -10px
        }

        .uni-py-5 {
            padding-top: 10px;
            padding-bottom: 10px
        }

        .uni-py-n5 {
            padding-top: -10px;
            padding-bottom: -10px
        }

        .uni-pa-5 {
            padding: 10px
        }

        .uni-pa-n5 {
            padding: -10px
        }

        .uni-pt-6 {
            padding-top: 12px
        }

        .uni-pt-n6 {
            padding-top: -12px
        }

        .uni-pr-6 {
            padding-right: 12px
        }

        .uni-pr-n6 {
            padding-right: -12px
        }

        .uni-pb-6 {
            padding-bottom: 12px
        }

        .uni-pb-n6 {
            padding-bottom: -12px
        }

        .uni-pl-6 {
            padding-left: 12px
        }

        .uni-pl-n6 {
            padding-left: -12px
        }

        .uni-px-6 {
            padding-left: 12px;
            padding-right: 12px
        }

        .uni-px-n6 {
            padding-left: -12px;
            padding-right: -12px
        }

        .uni-py-6 {
            padding-top: 12px;
            padding-bottom: 12px
        }

        .uni-py-n6 {
            padding-top: -12px;
            padding-bottom: -12px
        }

        .uni-pa-6 {
            padding: 12px
        }

        .uni-pa-n6 {
            padding: -12px
        }

        .uni-pt-7 {
            padding-top: 14px
        }

        .uni-pt-n7 {
            padding-top: -14px
        }

        .uni-pr-7 {
            padding-right: 14px
        }

        .uni-pr-n7 {
            padding-right: -14px
        }

        .uni-pb-7 {
            padding-bottom: 14px
        }

        .uni-pb-n7 {
            padding-bottom: -14px
        }

        .uni-pl-7 {
            padding-left: 14px
        }

        .uni-pl-n7 {
            padding-left: -14px
        }

        .uni-px-7 {
            padding-left: 14px;
            padding-right: 14px
        }

        .uni-px-n7 {
            padding-left: -14px;
            padding-right: -14px
        }

        .uni-py-7 {
            padding-top: 14px;
            padding-bottom: 14px
        }

        .uni-py-n7 {
            padding-top: -14px;
            padding-bottom: -14px
        }

        .uni-pa-7 {
            padding: 14px
        }

        .uni-pa-n7 {
            padding: -14px
        }

        .uni-pt-8 {
            padding-top: 16px
        }

        .uni-pt-n8 {
            padding-top: -16px
        }

        .uni-pr-8 {
            padding-right: 16px
        }

        .uni-pr-n8 {
            padding-right: -16px
        }

        .uni-pb-8 {
            padding-bottom: 16px
        }

        .uni-pb-n8 {
            padding-bottom: -16px
        }

        .uni-pl-8 {
            padding-left: 16px
        }

        .uni-pl-n8 {
            padding-left: -16px
        }

        .uni-px-8 {
            padding-left: 16px;
            padding-right: 16px
        }

        .uni-px-n8 {
            padding-left: -16px;
            padding-right: -16px
        }

        .uni-py-8 {
            padding-top: 16px;
            padding-bottom: 16px
        }

        .uni-py-n8 {
            padding-top: -16px;
            padding-bottom: -16px
        }

        .uni-pa-8 {
            padding: 16px
        }

        .uni-pa-n8 {
            padding: -16px
        }

        .uni-pt-9 {
            padding-top: 18px
        }

        .uni-pt-n9 {
            padding-top: -18px
        }

        .uni-pr-9 {
            padding-right: 18px
        }

        .uni-pr-n9 {
            padding-right: -18px
        }

        .uni-pb-9 {
            padding-bottom: 18px
        }

        .uni-pb-n9 {
            padding-bottom: -18px
        }

        .uni-pl-9 {
            padding-left: 18px
        }

        .uni-pl-n9 {
            padding-left: -18px
        }

        .uni-px-9 {
            padding-left: 18px;
            padding-right: 18px
        }

        .uni-px-n9 {
            padding-left: -18px;
            padding-right: -18px
        }

        .uni-py-9 {
            padding-top: 18px;
            padding-bottom: 18px
        }

        .uni-py-n9 {
            padding-top: -18px;
            padding-bottom: -18px
        }

        .uni-pa-9 {
            padding: 18px
        }

        .uni-pa-n9 {
            padding: -18px
        }

        .uni-pt-10 {
            padding-top: 20px
        }

        .uni-pt-n10 {
            padding-top: -20px
        }

        .uni-pr-10 {
            padding-right: 20px
        }

        .uni-pr-n10 {
            padding-right: -20px
        }

        .uni-pb-10 {
            padding-bottom: 20px
        }

        .uni-pb-n10 {
            padding-bottom: -20px
        }

        .uni-pl-10 {
            padding-left: 20px
        }

        .uni-pl-n10 {
            padding-left: -20px
        }

        .uni-px-10 {
            padding-left: 20px;
            padding-right: 20px
        }

        .uni-px-n10 {
            padding-left: -20px;
            padding-right: -20px
        }

        .uni-py-10 {
            padding-top: 20px;
            padding-bottom: 20px
        }

        .uni-py-n10 {
            padding-top: -20px;
            padding-bottom: -20px
        }

        .uni-pa-10 {
            padding: 20px
        }

        .uni-pa-n10 {
            padding: -20px
        }

        .uni-pt-11 {
            padding-top: 22px
        }

        .uni-pt-n11 {
            padding-top: -22px
        }

        .uni-pr-11 {
            padding-right: 22px
        }

        .uni-pr-n11 {
            padding-right: -22px
        }

        .uni-pb-11 {
            padding-bottom: 22px
        }

        .uni-pb-n11 {
            padding-bottom: -22px
        }

        .uni-pl-11 {
            padding-left: 22px
        }

        .uni-pl-n11 {
            padding-left: -22px
        }

        .uni-px-11 {
            padding-left: 22px;
            padding-right: 22px
        }

        .uni-px-n11 {
            padding-left: -22px;
            padding-right: -22px
        }

        .uni-py-11 {
            padding-top: 22px;
            padding-bottom: 22px
        }

        .uni-py-n11 {
            padding-top: -22px;
            padding-bottom: -22px
        }

        .uni-pa-11 {
            padding: 22px
        }

        .uni-pa-n11 {
            padding: -22px
        }

        .uni-pt-12 {
            padding-top: 24px
        }

        .uni-pt-n12 {
            padding-top: -24px
        }

        .uni-pr-12 {
            padding-right: 24px
        }

        .uni-pr-n12 {
            padding-right: -24px
        }

        .uni-pb-12 {
            padding-bottom: 24px
        }

        .uni-pb-n12 {
            padding-bottom: -24px
        }

        .uni-pl-12 {
            padding-left: 24px
        }

        .uni-pl-n12 {
            padding-left: -24px
        }

        .uni-px-12 {
            padding-left: 24px;
            padding-right: 24px
        }

        .uni-px-n12 {
            padding-left: -24px;
            padding-right: -24px
        }

        .uni-py-12 {
            padding-top: 24px;
            padding-bottom: 24px
        }

        .uni-py-n12 {
            padding-top: -24px;
            padding-bottom: -24px
        }

        .uni-pa-12 {
            padding: 24px
        }

        .uni-pa-n12 {
            padding: -24px
        }

        .uni-pt-13 {
            padding-top: 26px
        }

        .uni-pt-n13 {
            padding-top: -26px
        }

        .uni-pr-13 {
            padding-right: 26px
        }

        .uni-pr-n13 {
            padding-right: -26px
        }

        .uni-pb-13 {
            padding-bottom: 26px
        }

        .uni-pb-n13 {
            padding-bottom: -26px
        }

        .uni-pl-13 {
            padding-left: 26px
        }

        .uni-pl-n13 {
            padding-left: -26px
        }

        .uni-px-13 {
            padding-left: 26px;
            padding-right: 26px
        }

        .uni-px-n13 {
            padding-left: -26px;
            padding-right: -26px
        }

        .uni-py-13 {
            padding-top: 26px;
            padding-bottom: 26px
        }

        .uni-py-n13 {
            padding-top: -26px;
            padding-bottom: -26px
        }

        .uni-pa-13 {
            padding: 26px
        }

        .uni-pa-n13 {
            padding: -26px
        }

        .uni-pt-14 {
            padding-top: 28px
        }

        .uni-pt-n14 {
            padding-top: -28px
        }

        .uni-pr-14 {
            padding-right: 28px
        }

        .uni-pr-n14 {
            padding-right: -28px
        }

        .uni-pb-14 {
            padding-bottom: 28px
        }

        .uni-pb-n14 {
            padding-bottom: -28px
        }

        .uni-pl-14 {
            padding-left: 28px
        }

        .uni-pl-n14 {
            padding-left: -28px
        }

        .uni-px-14 {
            padding-left: 28px;
            padding-right: 28px
        }

        .uni-px-n14 {
            padding-left: -28px;
            padding-right: -28px
        }

        .uni-py-14 {
            padding-top: 28px;
            padding-bottom: 28px
        }

        .uni-py-n14 {
            padding-top: -28px;
            padding-bottom: -28px
        }

        .uni-pa-14 {
            padding: 28px
        }

        .uni-pa-n14 {
            padding: -28px
        }

        .uni-pt-15 {
            padding-top: 30px
        }

        .uni-pt-n15 {
            padding-top: -30px
        }

        .uni-pr-15 {
            padding-right: 30px
        }

        .uni-pr-n15 {
            padding-right: -30px
        }

        .uni-pb-15 {
            padding-bottom: 30px
        }

        .uni-pb-n15 {
            padding-bottom: -30px
        }

        .uni-pl-15 {
            padding-left: 30px
        }

        .uni-pl-n15 {
            padding-left: -30px
        }

        .uni-px-15 {
            padding-left: 30px;
            padding-right: 30px
        }

        .uni-px-n15 {
            padding-left: -30px;
            padding-right: -30px
        }

        .uni-py-15 {
            padding-top: 30px;
            padding-bottom: 30px
        }

        .uni-py-n15 {
            padding-top: -30px;
            padding-bottom: -30px
        }

        .uni-pa-15 {
            padding: 30px
        }

        .uni-pa-n15 {
            padding: -30px
        }

        .uni-pt-16 {
            padding-top: 32px
        }

        .uni-pt-n16 {
            padding-top: -32px
        }

        .uni-pr-16 {
            padding-right: 32px
        }

        .uni-pr-n16 {
            padding-right: -32px
        }

        .uni-pb-16 {
            padding-bottom: 32px
        }

        .uni-pb-n16 {
            padding-bottom: -32px
        }

        .uni-pl-16 {
            padding-left: 32px
        }

        .uni-pl-n16 {
            padding-left: -32px
        }

        .uni-px-16 {
            padding-left: 32px;
            padding-right: 32px
        }

        .uni-px-n16 {
            padding-left: -32px;
            padding-right: -32px
        }

        .uni-py-16 {
            padding-top: 32px;
            padding-bottom: 32px
        }

        .uni-py-n16 {
            padding-top: -32px;
            padding-bottom: -32px
        }

        .uni-pa-16 {
            padding: 32px
        }

        .uni-pa-n16 {
            padding: -32px
        }

        .uni-radius-0 {
            border-radius: 0
        }

        .uni-radius {
            border-radius: 5px
        }

        .uni-radius-lg {
            border-radius: 10px
        }

        .uni-radius-xl {
            border-radius: 30px
        }

        .uni-radius-pill {
            border-radius: 9999px
        }

        .uni-radius-circle {
            border-radius: 50%
        }

        .uni-radius-t-0 {
            border-top-left-radius: 0;
            border-top-right-radius: 0
        }

        .uni-radius-t {
            border-top-left-radius: 5px;
            border-top-right-radius: 5px
        }

        .uni-radius-t-lg {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px
        }

        .uni-radius-t-xl {
            border-top-left-radius: 30px;
            border-top-right-radius: 30px
        }

        .uni-radius-t-pill {
            border-top-left-radius: 9999px;
            border-top-right-radius: 9999px
        }

        .uni-radius-t-circle {
            border-top-left-radius: 50%;
            border-top-right-radius: 50%
        }

        .uni-radius-r-0 {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0
        }

        .uni-radius-r {
            border-top-right-radius: 5px;
            border-bottom-right-radius: 5px
        }

        .uni-radius-r-lg {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px
        }

        .uni-radius-r-xl {
            border-top-right-radius: 30px;
            border-bottom-right-radius: 30px
        }

        .uni-radius-r-pill {
            border-top-right-radius: 9999px;
            border-bottom-right-radius: 9999px
        }

        .uni-radius-r-circle {
            border-top-right-radius: 50%;
            border-bottom-right-radius: 50%
        }

        .uni-radius-b-0 {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0
        }

        .uni-radius-b {
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px
        }

        .uni-radius-b-lg {
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px
        }

        .uni-radius-b-xl {
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px
        }

        .uni-radius-b-pill {
            border-bottom-left-radius: 9999px;
            border-bottom-right-radius: 9999px
        }

        .uni-radius-b-circle {
            border-bottom-left-radius: 50%;
            border-bottom-right-radius: 50%
        }

        .uni-radius-l-0 {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0
        }

        .uni-radius-l {
            border-top-left-radius: 5px;
            border-bottom-left-radius: 5px
        }

        .uni-radius-l-lg {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px
        }

        .uni-radius-l-xl {
            border-top-left-radius: 30px;
            border-bottom-left-radius: 30px
        }

        .uni-radius-l-pill {
            border-top-left-radius: 9999px;
            border-bottom-left-radius: 9999px
        }

        .uni-radius-l-circle {
            border-top-left-radius: 50%;
            border-bottom-left-radius: 50%
        }

        .uni-radius-tl-0 {
            border-top-left-radius: 0
        }

        .uni-radius-tl {
            border-top-left-radius: 5px
        }

        .uni-radius-tl-lg {
            border-top-left-radius: 10px
        }

        .uni-radius-tl-xl {
            border-top-left-radius: 30px
        }

        .uni-radius-tl-pill {
            border-top-left-radius: 9999px
        }

        .uni-radius-tl-circle {
            border-top-left-radius: 50%
        }

        .uni-radius-tr-0 {
            border-top-right-radius: 0
        }

        .uni-radius-tr {
            border-top-right-radius: 5px
        }

        .uni-radius-tr-lg {
            border-top-right-radius: 10px
        }

        .uni-radius-tr-xl {
            border-top-right-radius: 30px
        }

        .uni-radius-tr-pill {
            border-top-right-radius: 9999px
        }

        .uni-radius-tr-circle {
            border-top-right-radius: 50%
        }

        .uni-radius-br-0 {
            border-bottom-right-radius: 0
        }

        .uni-radius-br {
            border-bottom-right-radius: 5px
        }

        .uni-radius-br-lg {
            border-bottom-right-radius: 10px
        }

        .uni-radius-br-xl {
            border-bottom-right-radius: 30px
        }

        .uni-radius-br-pill {
            border-bottom-right-radius: 9999px
        }

        .uni-radius-br-circle {
            border-bottom-right-radius: 50%
        }

        .uni-radius-bl-0 {
            border-bottom-left-radius: 0
        }

        .uni-radius-bl {
            border-bottom-left-radius: 5px
        }

        .uni-radius-bl-lg {
            border-bottom-left-radius: 10px
        }

        .uni-radius-bl-xl {
            border-bottom-left-radius: 30px
        }

        .uni-radius-bl-pill {
            border-bottom-left-radius: 9999px
        }

        .uni-radius-bl-circle {
            border-bottom-left-radius: 50%
        }

        .uni-h1 {
            font-size: 32px;
            font-weight: 300;
            line-height: 50px
        }

        .uni-h2 {
            font-size: 28px;
            font-weight: 300;
            line-height: 40px
        }

        .uni-h3 {
            font-size: 24px;
            font-weight: 400;
            line-height: 32px
        }

        .uni-h4 {
            font-size: 20px;
            font-weight: 400;
            line-height: 30px
        }

        .uni-h5 {
            font-size: 16px;
            font-weight: 400;
            line-height: 24px
        }

        .uni-h6 {
            font-size: 14px;
            font-weight: 500;
            line-height: 18px
        }

        .uni-subtitle {
            font-size: 12px;
            font-weight: 400;
            line-height: 20px
        }

        .uni-body {
            font-size: 14px;
            font-weight: 400;
            line-height: 22px
        }

        .uni-caption {
            font-size: 12px;
            font-weight: 400;
            line-height: 20px
        }

        .uni-btn {
            margin: 5px;
            color: #393939;
            border: 1px solid #ccc;
            font-size: 16px;
            font-weight: 200;
            background-color: #f9f9f9;
            overflow: visible
        }

        .uni-btn::after {
            border: none
        }

        .uni-btn:not([type]),
        .uni-btn[type=default] {
            color: #999
        }

        .uni-btn:not([type])[loading],
        .uni-btn[type=default][loading] {
            background: none
        }

        .uni-btn:not([type])[loading]::before,
        .uni-btn[type=default][loading]::before {
            margin-right: 5px
        }

        .uni-btn:not([type])[disabled],
        .uni-btn[type=default][disabled] {
            color: #d6d6d6
        }

        .uni-btn:not([type])[disabled],
        .uni-btn:not([type])[disabled][loading],
        .uni-btn:not([type])[disabled]:active,
        .uni-btn[type=default][disabled],
        .uni-btn[type=default][disabled][loading],
        .uni-btn[type=default][disabled]:active {
            color: #d6d6d6;
            background-color: #fafafa;
            border-color: #f0f0f0
        }

        .uni-btn:not([type])[plain],
        .uni-btn[type=default][plain] {
            color: #999;
            background: none;
            border-color: #f0f0f0
        }

        .uni-btn:not([type])[plain]:not([hover-class]):active,
        .uni-btn[type=default][plain]:not([hover-class]):active {
            background: none;
            color: #ccc;
            border-color: #e6e6e6;
            outline: none
        }

        .uni-btn:not([type])[plain][disabled],
        .uni-btn:not([type])[plain][disabled][loading],
        .uni-btn:not([type])[plain][disabled]:active,
        .uni-btn[type=default][plain][disabled],
        .uni-btn[type=default][plain][disabled][loading],
        .uni-btn[type=default][plain][disabled]:active {
            background: none;
            color: #d6d6d6;
            border-color: #f0f0f0
        }

        .uni-btn:not([hover-class]):active {
            color: grey
        }

        .uni-btn[size=mini] {
            font-size: 16px;
            font-weight: 200;
            border-radius: 8px
        }

        .uni-btn.uni-btn-small {
            font-size: 14px
        }

        .uni-btn.uni-btn-mini {
            font-size: 12px
        }

        .uni-btn.uni-btn-radius {
            border-radius: 999px
        }

        .uni-btn[type=primary] {
            color: #fff;
            background-color: #2979ff;
            border-color: #266feb
        }

        .uni-btn[type=primary]:not([hover-class]):active {
            background: #256de6;
            border-color: #2161cc;
            color: #fff;
            outline: none
        }

        .uni-btn[type=primary][loading] {
            color: #fff;
            background-color: #2979ff;
            border-color: #266feb
        }

        .uni-btn[type=primary][loading]:not([hover-class]):active {
            background: #256de6;
            border-color: #2161cc;
            color: #fff;
            outline: none
        }

        .uni-btn[type=primary][loading]::before {
            margin-right: 5px
        }

        .uni-btn[type=primary][disabled],
        .uni-btn[type=primary][disabled][loading],
        .uni-btn[type=primary][disabled]:not([hover-class]):active {
            color: #fff;
            border-color: #80adfa;
            background-color: #94bcff
        }

        .uni-btn[type=primary][plain] {
            color: #2979ff;
            background-color: #eaf2ff;
            border-color: #bfd7ff
        }

        .uni-btn[type=primary][plain]:not([hover-class]):active {
            background: #d4e4ff;
            color: #2979ff;
            outline: none;
            border-color: #94bcff
        }

        .uni-btn[type=primary][plain][loading] {
            color: #2979ff;
            background-color: #eaf2ff;
            border-color: #bfd7ff
        }

        .uni-btn[type=primary][plain][loading]:not([hover-class]):active {
            background: #d4e4ff;
            color: #2979ff;
            outline: none;
            border-color: #94bcff
        }

        .uni-btn[type=primary][plain][loading]::before {
            margin-right: 5px
        }

        .uni-btn[type=primary][plain][disabled],
        .uni-btn[type=primary][plain][disabled]:active {
            color: #7fafff;
            background-color: #eaf2ff;
            border-color: #d4e4ff
        }

        .uni-btn[type=success] {
            color: #fff;
            background-color: #18bc37;
            border-color: #16ad33
        }

        .uni-btn[type=success]:not([hover-class]):active {
            background: #16a932;
            border-color: #13962c;
            color: #fff;
            outline: none
        }

        .uni-btn[type=success][loading] {
            color: #fff;
            background-color: #18bc37;
            border-color: #16ad33
        }

        .uni-btn[type=success][loading]:not([hover-class]):active {
            background: #16a932;
            border-color: #13962c;
            color: #fff;
            outline: none
        }

        .uni-btn[type=success][loading]::before {
            margin-right: 5px
        }

        .uni-btn[type=success][disabled],
        .uni-btn[type=success][disabled][loading],
        .uni-btn[type=success][disabled]:not([hover-class]):active {
            color: #fff;
            border-color: #89c794;
            background-color: #8cde9b
        }

        .uni-btn[type=success][plain] {
            color: #18bc37;
            background-color: #e8f8eb;
            border-color: #baebc3
        }

        .uni-btn[type=success][plain]:not([hover-class]):active {
            background: #d1f2d7;
            color: #18bc37;
            outline: none;
            border-color: #8cde9b
        }

        .uni-btn[type=success][plain][loading] {
            color: #18bc37;
            background-color: #e8f8eb;
            border-color: #baebc3
        }

        .uni-btn[type=success][plain][loading]:not([hover-class]):active {
            background: #d1f2d7;
            color: #18bc37;
            outline: none;
            border-color: #8cde9b
        }

        .uni-btn[type=success][plain][loading]::before {
            margin-right: 5px
        }

        .uni-btn[type=success][plain][disabled],
        .uni-btn[type=success][plain][disabled]:active {
            color: #74d787;
            background-color: #e8f8eb;
            border-color: #d1f2d7
        }

        .uni-btn[type=error] {
            color: #fff;
            background-color: #e43d33;
            border-color: #d2382f
        }

        .uni-btn[type=error]:not([hover-class]):active {
            background: #cd372e;
            border-color: #b63129;
            color: #fff;
            outline: none
        }

        .uni-btn[type=error][loading] {
            color: #fff;
            background-color: #e43d33;
            border-color: #d2382f
        }

        .uni-btn[type=error][loading]:not([hover-class]):active {
            background: #cd372e;
            border-color: #b63129;
            color: #fff;
            outline: none
        }

        .uni-btn[type=error][loading]::before {
            margin-right: 5px
        }

        .uni-btn[type=error][disabled],
        .uni-btn[type=error][disabled][loading],
        .uni-btn[type=error][disabled]:not([hover-class]):active {
            color: #fff;
            border-color: #e4928d;
            background-color: #f29e99
        }

        .uni-btn[type=error][plain] {
            color: #e43d33;
            background-color: #fceceb;
            border-color: #f7c5c2
        }

        .uni-btn[type=error][plain]:not([hover-class]):active {
            background: #fad8d6;
            color: #e43d33;
            outline: none;
            border-color: #f29e99
        }

        .uni-btn[type=error][plain][loading] {
            color: #e43d33;
            background-color: #fceceb;
            border-color: #f7c5c2
        }

        .uni-btn[type=error][plain][loading]:not([hover-class]):active {
            background: #fad8d6;
            color: #e43d33;
            outline: none;
            border-color: #f29e99
        }

        .uni-btn[type=error][plain][loading]::before {
            margin-right: 5px
        }

        .uni-btn[type=error][plain][disabled],
        .uni-btn[type=error][plain][disabled]:active {
            color: #ef8b85;
            background-color: #fceceb;
            border-color: #fad8d6
        }

        .uni-btn[type=warning] {
            color: #fff;
            background-color: #f3a73f;
            border-color: #e09a3a
        }

        .uni-btn[type=warning]:not([hover-class]):active {
            background: #db9639;
            border-color: #c28632;
            color: #fff;
            outline: none
        }

        .uni-btn[type=warning][loading] {
            color: #fff;
            background-color: #f3a73f;
            border-color: #e09a3a
        }

        .uni-btn[type=warning][loading]:not([hover-class]):active {
            background: #db9639;
            border-color: #c28632;
            color: #fff;
            outline: none
        }

        .uni-btn[type=warning][loading]::before {
            margin-right: 5px
        }

        .uni-btn[type=warning][disabled],
        .uni-btn[type=warning][disabled][loading],
        .uni-btn[type=warning][disabled]:not([hover-class]):active {
            color: #fff;
            border-color: #f8c887;
            background-color: #f9d39f
        }

        .uni-btn[type=warning][plain] {
            color: #f3a73f;
            background-color: #fef6ec;
            border-color: #fbe5c5
        }

        .uni-btn[type=warning][plain]:not([hover-class]):active {
            background: #fdedd9;
            color: #f3a73f;
            outline: none;
            border-color: #f9d39f
        }

        .uni-btn[type=warning][plain][loading] {
            color: #f3a73f;
            background-color: #fef6ec;
            border-color: #fbe5c5
        }

        .uni-btn[type=warning][plain][loading]:not([hover-class]):active {
            background: #fdedd9;
            color: #f3a73f;
            outline: none;
            border-color: #f9d39f
        }

        .uni-btn[type=warning][plain][loading]::before {
            margin-right: 5px
        }

        .uni-btn[type=warning][plain][disabled],
        .uni-btn[type=warning][plain][disabled]:active {
            color: #f8ca8c;
            background-color: #fef6ec;
            border-color: #fdedd9
        }

        .uni-btn[type=info] {
            color: #fff;
            background-color: #8f939c;
            border-color: #848790
        }

        .uni-btn[type=info]:not([hover-class]):active {
            background: #81848c;
            border-color: #72767d;
            color: #fff;
            outline: none
        }

        .uni-btn[type=info][loading] {
            color: #fff;
            background-color: #8f939c;
            border-color: #848790
        }

        .uni-btn[type=info][loading]:not([hover-class]):active {
            background: #81848c;
            border-color: #72767d;
            color: #fff;
            outline: none
        }

        .uni-btn[type=info][loading]::before {
            margin-right: 5px
        }

        .uni-btn[type=info][disabled],
        .uni-btn[type=info][disabled][loading],
        .uni-btn[type=info][disabled]:not([hover-class]):active {
            color: #fff;
            border-color: #babcc1;
            background-color: #c7c9ce
        }

        .uni-btn[type=info][plain] {
            color: #8f939c;
            background-color: #f4f4f5;
            border-color: #dddfe1
        }

        .uni-btn[type=info][plain]:not([hover-class]):active {
            background: #e9e9eb;
            color: #8f939c;
            outline: none;
            border-color: #c7c9ce
        }

        .uni-btn[type=info][plain][loading] {
            color: #8f939c;
            background-color: #f4f4f5;
            border-color: #dddfe1
        }

        .uni-btn[type=info][plain][loading]:not([hover-class]):active {
            background: #e9e9eb;
            color: #8f939c;
            outline: none;
            border-color: #c7c9ce
        }

        .uni-btn[type=info][plain][loading]::before {
            margin-right: 5px
        }

        .uni-btn[type=info][plain][disabled],
        .uni-btn[type=info][plain][disabled]:active {
            color: #bcbec4;
            background-color: #f4f4f5;
            border-color: #e9e9eb
        }

        uni-page-body {
            background-color: #f5f5f5
        }

        body {
            background-color: #f5f5f5
        }

        .box_column {
            display: flex;
            flex-direction: column
        }

        .box_row {
            display: flex;
            flex-direction: row
        }

        .example-info {
            font-size: 14px;
            color: #333;
            padding: 10px
        }
    </style>
    <script charset="utf-8"
            src="./h61.amkj.store_pages_refund_svRefund_files/pages-index-cg~pages-index-cgt~pages-index-charge~pages-index-chargeTel~pages-index-dial~pages-index~85c979f2.1401b955.js.下载"></script>
    <script charset="utf-8"
            src="./h61.amkj.store_pages_refund_svRefund_files/pages-refund-svRefund.6cceee6c.js.下载"></script>
    <style type="text/css">
        .uni-app--showtabbar uni-page-wrapper {
            display: block;
            height: calc(100% - 50px);
            height: calc(100% - 50px - constant(safe-area-inset-bottom));
            height: calc(100% - 50px - env(safe-area-inset-bottom));
        }

        .uni-app--showtabbar uni-page-wrapper::after {
            content: "";
            display: block;
            width: 100%;
            height: 50px;
            height: calc(50px + constant(safe-area-inset-bottom));
            height: calc(50px + env(safe-area-inset-bottom));
        }

        .uni-app--showtabbar uni-page-head[uni-page-head-type="default"] ~ uni-page-wrapper {
            height: calc(100% - 44px - 50px);
            height: calc(100% - 44px - constant(safe-area-inset-top) - 50px - constant(safe-area-inset-bottom));
            height: calc(100% - 44px - env(safe-area-inset-top) - 50px - env(safe-area-inset-bottom));
        }
    </style>
    <style type="text/css">
        @charset "UTF-8";

        /* 水平间距 */
        /* 水平间距 */
        .uni-popup[data-v-640cd216] {
            position: fixed;
            z-index: 99
        }

        .m-toast-pop {
            display: none;
            position: fixed;
            width: 100%;
            top: 0;
            bottom: 0;
            right: 0;
            overflow: auto;
            text-align: center;
        }

        .m-toast-inner {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 100%;
            transform: translate(-50%, -50%);
            -webkit-transform: translate(-50%, -50%);
            text-align: center;
        }

        .m-toast-inner-text {
            display: inline-block;
            margin: 0 22px;
            padding: 19px 21px;
            font-size: 16px;
            color: #FFFFFF;
            letter-spacing: 0;
            line-height: 22px;
            background: rgba(0, 0, 0, 0.72);
            border-radius: 10px;
        }


        .uni-popup.top[data-v-640cd216],
        .uni-popup.left[data-v-640cd216],
        .uni-popup.right[data-v-640cd216] {
            top: var(--window-top)
        }

        .uni-popup .uni-popup__wrapper[data-v-640cd216] {
            display: block;
            position: relative
            /* iphonex 等安全区设置，底部安全区适配 */
        }

        .uni-popup .uni-popup__wrapper.left[data-v-640cd216],
        .uni-popup .uni-popup__wrapper.right[data-v-640cd216] {
            padding-top: var(--window-top);
            flex: 1
        }

        .fixforpc-z-index[data-v-640cd216] {
            z-index: 999
        }

        .fixforpc-top[data-v-640cd216] {
            top: 0
        }
    </style>
</head>

<body class="uni-body pages-refund-svRefund">
<noscript><strong>Please enable JavaScript to
        continue.</strong></noscript>
<uni-app class="uni-app--maxwidth">
    <uni-page
            data-page="pages/refund/svRefund"><!----><!---->
        <uni-page-wrapper>
            <uni-page-body>
                <uni-view data-v-7a41c382=""
                          class="box_column"
                          style="align-items: center; background-color: white; padding-left: 12px; padding-right: 12px;">
                    <uni-image
                            data-v-7a41c382=""
                            style="width: 10px; height: 16px; align-self: flex-start; margin-top: 10px;">
                        <div
                                style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAgCAMAAAAlvKiEAAAAZlBMVEUAAAAhISEyMjIyMjIyMjIzMzMxMTEvLy8xMTEwMDAwMDAvLy8yMjIyMjIxMTEvLy8yMjIyMjIwMDAwMDAuLi4yMjIzMzMyMjIzMzMyMjIyMjIyMjIyMjIwMDAwMDAzMzMAAAAzMzMj08qfAAAAIXRSTlMACO+9+fK6MT03NBvheXAOrJJVKiP009HOooRlW09FLQRpjFwJAAAAiElEQVQoz5XPyRKDIBCE4QEREQi4ZV95/5dM5zijqZL/+F26mnZ0OD9XlkoxQdhUgAs3Z4B3bm8Nu3ELEXbllk8w+2GmGtglE2uAxcBthOlZHIEZx+1R0MTtZWCJm29hI7dFwwZajxyzwM5ArRLq9ZZ2tdrsU1+hfVuplkTup/2GRkWyOQX60xcIvA5xvlsnuQAAAABJRU5ErkJggg==&quot;); background-position: 0% 0%; background-size: 100% 100%; background-repeat: no-repeat;">
                        </div><!---->
                        <img
                                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAgCAMAAAAlvKiEAAAAZlBMVEUAAAAhISEyMjIyMjIyMjIzMzMxMTEvLy8xMTEwMDAwMDAvLy8yMjIyMjIxMTEvLy8yMjIyMjIwMDAwMDAuLi4yMjIzMzMyMjIzMzMyMjIyMjIyMjIyMjIwMDAwMDAzMzMAAAAzMzMj08qfAAAAIXRSTlMACO+9+fK6MT03NBvheXAOrJJVKiP009HOooRlW09FLQRpjFwJAAAAiElEQVQoz5XPyRKDIBCE4QEREQi4ZV95/5dM5zijqZL/+F26mnZ0OD9XlkoxQdhUgAs3Z4B3bm8Nu3ELEXbllk8w+2GmGtglE2uAxcBthOlZHIEZx+1R0MTtZWCJm29hI7dFwwZajxyzwM5ArRLq9ZZ2tdrsU1+hfVuplkTup/2GRkWyOQX60xcIvA5xvlsnuQAAAABJRU5ErkJggg=="
                                draggable="false">
                    </uni-image>
                    <uni-text data-v-7a41c382=""
                              style="color: rgb(10, 10, 10); font-size: 25px; font-weight: bold; margin-top: 105px;">
                        <span>自助退款申请</span>
                    </uni-text>
                    <uni-input data-v-7a41c382="" class="uni-input"
                               style="color: rgb(49, 49, 49); font-size: 14px; margin-top: 37px; width: 225px;">
                        <div class="uni-input-wrapper">
                            <!-- <div class="uni-input-placeholder input-placeholder" data-v-7a41c382="">请输入支付宝或微信支付交易单号</div> -->
                            <input id="payinput" maxlength="30" step="" enterkeyhint="done" autocomplete="off"
                                   class="uni-input-input" placeholder="请输入支付宝或微信支付交易单号"><!---->
                        </div>
                    </uni-input>
                    <uni-view data-v-7a41c382=""
                              style="background-color: rgb(204, 204, 204); width: 100%; height: 1px; margin-top: 19px;"></uni-view>
                    <uni-text id="paybutton" data-v-7a41c382=""
                              style="color: white; font-size: 29px; text-align: center; width: 100%; margin-top: 44px; padding-top: 19px; padding-bottom: 19px; border-radius: 28px; background-color: rgb(64, 135, 240);">
                        <span>立即退款</span>
                    </uni-text>
                    <uni-text data-v-7a41c382=""
                              style="color: rgb(10, 10, 10); font-size: 12px; font-weight: bold; margin-top: 25px; align-self: flex-start;">
                        <span>注意事项：</span>
                    </uni-text>
                    <uni-text data-v-7a41c382="" style="color: rgb(204, 204, 204); font-size: 12px; margin-top: 12px;">
                      <span>1.本页面为自助退款页面，无需人工审核。提交退费申请后，相关费用将在10分钟内原路退回到您的支付账户中。<br>
                        2.若输入的交易单号查询不到您的订单，请联系在线客服或拨打18704551088咨询人工客服联系退费。<br>
                        3.退款成功后，您购买的短剧服务将立即失效，感谢您的理解。
                      </span>
                    </uni-text>
                    <div id="m-toast-pop" class="m-toast-pop">
                        <div class="m-toast-inner">
                            <div class="m-toast-inner-text" id="m-toast-inner-text"></div>
                        </div>
                    </div>
                    <uni-text data-v-7a41c382=""
                              style="color: rgb(10, 10, 10); font-size: 25px; font-weight: bold; margin-top: 12px;">
                        <span>如何查看微信支付交易单号</span>
                    </uni-text>
                    <uni-cover-image data-v-7a41c382="" src="/static/img/iv_see_odd.a2cdcfda.png"
                                     style="height: 2763px; margin-top: 12px;">
                        <div class="uni-cover-image"><img
                                    src="./h61.amkj.store_pages_refund_svRefund_files/iv_see_odd.a2cdcfda.png"></div>
                    </uni-cover-image><!----><!---->
                </uni-view>

                <!-- 请求成功 -->
                <uni-view id="succ" hidden="" data-v-640cd216="" data-v-7a41c382="" class="uni-popup center">
                    <uni-view
                            data-v-640cd216="">
                        <uni-view data-v-289169e0="" data-v-640cd216="" class="" name="mask"
                                  style="opacity: 1; position: fixed; inset: 0px; background-color: rgba(0, 0, 0, 0.4); transition: opacity 300ms ease 0ms, -webkit-transform 300ms ease 0ms, transform 300ms ease 0ms; transform-origin: 50% 50%;"></uni-view>
                        <uni-view
                                data-v-289169e0="" data-v-640cd216="" class="" name="content"
                                style="transform: scale(1); opacity: 1; position: fixed; display: flex; flex-direction: column; inset: 0px; justify-content: center; align-items: center; transition: opacity 300ms ease 0ms, -webkit-transform 300ms ease 0ms, transform 300ms ease 0ms; transform-origin: 50% 50%;">
                            <uni-view
                                    data-v-640cd216="" class="uni-popup__wrapper center"
                                    style="background-color: transparent;">
                                <uni-view data-v-7a41c382="" class="box_column"
                                          style="align-items: center; background-color: white; border-radius: 16px; width: 334px; padding-bottom: 24px;">
                                    <uni-text
                                            data-v-7a41c382=""
                                            style="color: rgb(10, 10, 10); font-size: 24px; font-weight: bold; margin-top: 47px;">
                                        <span>申请成功</span></uni-text>
                                    <uni-image
                                            data-v-7a41c382=""
                                            style="width: 102px; height: 102px; margin-top: 24px;">
                                        <div
                                                style="background-image: url(&quot;./h61.amkj.store_pages_refund_svRefund_files/ic_fail.123f7583.png&quot;); background-position: 0% 0%; background-size: 100% 100%; background-repeat: no-repeat;">
                                        </div>

                                    </uni-image>
                                    <uni-text data-v-7a41c382=""
                                              style="color: rgb(10, 10, 10); font-size: 16px; margin-top: 36px; padding-left: 20px; padding-right: 19px;">
                                        <span>申请退款成功，将在一到七个工作日内退回原支付地址，请仔细查看</span>
                                    </uni-text>
                                    <uni-image
                                            id="succ_know"
                                            data-v-7a41c382=""
                                            style="width: 275px; height: 52px; margin-top: 47px;">
                                        <div
                                                style="background-image: url(&quot;h61.amkj.store_pages_refund_svRefund_files/iknow.png&quot;); background-position: 0% 0%; background-size: 100% 100%; background-repeat: no-repeat;">
                                        </div>
                                    </uni-image>
                                </uni-view>
                            </uni-view>
                        </uni-view>
                    </uni-view><!---->
                </uni-view>

                <!-- 请求失败 -->
                <uni-view hidden="" id="fail" data-v-640cd216="" data-v-7a41c382="" class="uni-popup center">
                    <uni-view
                            data-v-640cd216="">
                        <uni-view data-v-289169e0="" data-v-640cd216="" class="" name="mask"
                                  style="opacity: 1; position: fixed; inset: 0px; background-color: rgba(0, 0, 0, 0.4); transition: opacity 300ms ease 0ms, -webkit-transform 300ms ease 0ms, transform 300ms ease 0ms; transform-origin: 50% 50%;"></uni-view>
                        <uni-view
                                data-v-289169e0="" data-v-640cd216="" class="" name="content"
                                style="transform: scale(1); opacity: 1; position: fixed; display: flex; flex-direction: column; inset: 0px; justify-content: center; align-items: center; transition: opacity 300ms ease 0ms, -webkit-transform 300ms ease 0ms, transform 300ms ease 0ms; transform-origin: 50% 50%;">
                            <uni-view
                                    data-v-640cd216="" class="uni-popup__wrapper center"
                                    style="background-color: transparent;">
                                <uni-view data-v-7a41c382="" class="box_column"
                                          style="align-items: center; background-color: white; border-radius: 16px; width: 334px; padding-bottom: 24px;">
                                    <uni-text
                                            data-v-7a41c382=""
                                            style="color: rgb(10, 10, 10); font-size: 24px; font-weight: bold; margin-top: 47px;">
                                        <span>申请失败</span></uni-text>
                                    <uni-image
                                            data-v-7a41c382=""
                                            style="width: 102px; height: 102px; margin-top: 24px;">
                                        <div
                                                style="background-image: url(&quot;h61.amkj.store_pages_refund_svRefund_files/ic_fail.123f7583.png&quot;); background-position: 0% 0%; background-size: 100% 100%; background-repeat: no-repeat;">
                                        </div>

                                    </uni-image>
                                    <uni-text data-v-7a41c382=""
                                              style="color: rgb(10, 10, 10); font-size: 16px; margin-top: 36px; padding-left: 20px; padding-right: 19px;">
                                        <span>未查到符合自助退款要求的订单，如需退款，请联系在线客服或拨打客服热线。</span>
                                    </uni-text>
                                    <uni-image
                                            id="fail_know"
                                            data-v-7a41c382=""
                                            style="width: 275px; height: 52px; margin-top: 47px;">
                                        <div
                                                style="background-image: url(&quot;h61.amkj.store_pages_refund_svRefund_files/iknow.png&quot;); background-position: 0% 0%; background-size: 100% 100%; background-repeat: no-repeat;">
                                        </div>
                                    </uni-image>
                                </uni-view>
                            </uni-view>
                        </uni-view>
                    </uni-view><!----></uni-view>
            </uni-page-body>
        </uni-page-wrapper>
    </uni-page><!----><!---->
    <uni-actionsheet>
        <div class="uni-mask uni-actionsheet__mask" style="display: none;"></div>
        <div class="uni-actionsheet">
            <div class="uni-actionsheet__menu"><!----><!---->
                <div style="max-height: 260px; overflow: hidden;">
                    <div style="transform: translateY(0px) translateZ(0px);"></div>
                </div>
            </div>
            <div class="uni-actionsheet__action">
                <div class="uni-actionsheet__cell" style="color: rgb(0, 0, 0);"> 取消</div>
            </div>
            <div></div>
        </div>
    </uni-actionsheet>
    <uni-modal style="display: none;">
        <div class="uni-mask"></div>
        <div class="uni-modal"><!---->
            <div class="uni-modal__bd"></div>
            <div class="uni-modal__ft">
                <div class="uni-modal__btn uni-modal__btn_default" style="color: rgb(0, 0, 0);"> Cancel</div>
                <div class="uni-modal__btn uni-modal__btn_primary" style="color: rgb(0, 122, 255);"> OK</div>
            </div>
        </div><!---->
    </uni-modal><!----><!----></uni-app>

<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.js"></script>
<div
        style="position: absolute; left: 0px; top: 0px; width: 0px; height: 0px; z-index: -1; overflow: hidden; visibility: hidden;">
    <div
            style="position: absolute; width: 100px; height: 200px; box-sizing: border-box; overflow: hidden; padding-bottom: env(safe-area-inset-top);">
        <div
                style="transition: all 0s ease 0s; animation: 0s ease 0s 1 normal none running none; width: 400px; height: 400px;">
        </div>
    </div>
    <div
            style="position: absolute; width: 100px; height: 200px; box-sizing: border-box; overflow: hidden; padding-bottom: env(safe-area-inset-top);">
        <div
                style="transition: all 0s ease 0s; animation: 0s ease 0s 1 normal none running none; width: 250%; height: 250%;">
        </div>
    </div>
    <div
            style="position: absolute; width: 100px; height: 200px; box-sizing: border-box; overflow: hidden; padding-bottom: env(safe-area-inset-left);">
        <div
                style="transition: all 0s ease 0s; animation: 0s ease 0s 1 normal none running none; width: 400px; height: 400px;">
        </div>
    </div>
    <div
            style="position: absolute; width: 100px; height: 200px; box-sizing: border-box; overflow: hidden; padding-bottom: env(safe-area-inset-left);">
        <div
                style="transition: all 0s ease 0s; animation: 0s ease 0s 1 normal none running none; width: 250%; height: 250%;">
        </div>
    </div>
    <div
            style="position: absolute; width: 100px; height: 200px; box-sizing: border-box; overflow: hidden; padding-bottom: env(safe-area-inset-right);">
        <div
                style="transition: all 0s ease 0s; animation: 0s ease 0s 1 normal none running none; width: 400px; height: 400px;">
        </div>
    </div>
    <div
            style="position: absolute; width: 100px; height: 200px; box-sizing: border-box; overflow: hidden; padding-bottom: env(safe-area-inset-right);">
        <div
                style="transition: all 0s ease 0s; animation: 0s ease 0s 1 normal none running none; width: 250%; height: 250%;">
        </div>
    </div>
    <div
            style="position: absolute; width: 100px; height: 200px; box-sizing: border-box; overflow: hidden; padding-bottom: env(safe-area-inset-bottom);">
        <div
                style="transition: all 0s ease 0s; animation: 0s ease 0s 1 normal none running none; width: 400px; height: 400px;">
        </div>
    </div>
    <div
            style="position: absolute; width: 100px; height: 200px; box-sizing: border-box; overflow: hidden; padding-bottom: env(safe-area-inset-bottom);">
        <div
                style="transition: all 0s ease 0s; animation: 0s ease 0s 1 normal none running none; width: 250%; height: 250%;">
        </div>
    </div>
</div>


<script>
    const payinput = document.getElementById("payinput")
    const paybutton = document.getElementById("paybutton")
    let domain = "<?php echo $domain;?>"

    $('#paybutton').on('click', function () {
        if (payinput.value.length === 0) {
            $('#m-toast-inner-text').text('请输入单号');
            $('#m-toast-pop').fadeIn();
            setTimeout(function () {
                $('#m-toast-pop').fadeOut();
            }, 1500);
        } else {
            window.location.href = "https://" + domain + "/svRefund/svRefund.php?paynum=" + payinput.value
        }
    });

    let result = "<?php echo $result;?>"

    if (result === 'true') {
        $('#succ').show()
    } else if (result === 'false') {
        $('#fail').show()
    }

    // 隐藏fail
    $('#fail_know').on('click', function () {
        $('#fail').hide()
    })

    // 隐藏succ
    $('#succ_know').on('click', function () {
        $('#succ').hide()
    })

</script>
</body>

</html>