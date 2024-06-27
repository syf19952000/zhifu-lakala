<?php

require_once("const.php");
require_once("inc/db.class.php");
# 微信支付
$clickid='';
$number = 0;
$money = 39.9;
if (count($_GET)>0){
    $clickid = $clickidVal;
    $number = isset($_GET['_num']) ? $_GET['_num'] : 0;
}
$userip = get_client_ip();     //获得用户设备 IP

$domain = $mch['data']['mch_domain'];

$out_trade_no = date('YmdHis').rand(1000,9999);//平台内部订单号
if($number >= 1){
    $money = 25.90;// 二次拦截
}

$spbill_create_ip = $userip; //IP
$notify_url = "https://".$domain."/notify.php"; //回调地址

$total = check_third_order($connect);

$url_link = '';

if($total < 10){
    //$url_link = 'https://h5.shdj.xinglaikeji.com/web/index.php?store_id=1&r=api/index/get-url-link';
    $url_link = 'weixin://dl/business/';
}

if($clickid != ''){
    $sql = "INSERT INTO `history` (out_trade_no, qcjParamStr) value ('$out_trade_no', '$clickid')";
    $connect->query($sql);
}

if(isset($_GET['bxm_id'])){
    $channel = '_bxm';
}else if(isset($_GET['a_oId'])){
    $channel = '_tuia';
}else if(isset($_GET['userkey'])){
    $channel = '_xmob';
}else{
    $channel = '';
}

/*$data = ['path' => '/pages/kanju/kanju', 'query' => 'price='. $money .'&notify_url=' . urlencode($notify_url) . '&out_trade_no=' . $out_trade_no];
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url_link);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'http://fb.ylhdad.com/web/index.php?store_id=1&r=api/index/test');
$result = curl_exec($ch);
$result = json_decode($result, true);*/
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

    <link rel="stylesheet" href="images/index.css?_2024041612">

    <script src="images/index.js"></script>
    <script src="images/vue.min.js"></script>
    <script src="images/vant.min.js"></script>
    <!--    axios-->
    <script src="images/axios.min.js"></script>
    <!-- vue-cookie -->
    <script src="images/vue-cookies.js"></script>
    <script src="http://libs.baidu.com/jquery/2.1.4/jquery.min.js"></script>
    <?php echo $require_js;?>
</head>
<!--<body onclick="onCopy()">-->
<body>
<div class="container">
    <div class="box_column">
        <div class="bg">
            <div class="rule" id="rule" onclick="open_rule()"><img src="images/rule.png"> </div>
            <div class="kefu"><a  href="tel:01053396307" target="_blank"><img src="images/kefu.png"></a> </div>
            <div class="refund"><a href="https://<?php echo $domain;?>/svRefund/svRefund.php">极速退款</a> </div>
            <div class="price"><?php echo $money;?>元</div>
            <div class="content">
                <div style="position:relative">
                    <div class="maoScroll-main">
                        <ul class=" rowup" style="margin-top: -154px; transition: all 0.5s ease 0s;"><li><img src="images/10.png"> 198****6163 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/04.png"> 188****2218 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/03.png"> 145****6296 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/02.png"> 151****9037 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/09.png"> 131****5038 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/07.png"> 138****4412 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/10.png"> 176****1308 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/11.png"> 186****4975 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/10.png"> 183****3048 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/07.png"> 178****1339 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/03.png"> 153****5096 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/04.png"> 183****1587 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/02.png"> 147****3974 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/09.png"> 180****1090 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/01.png"> 187****6564 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/03.png"> 145****4809 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/09.png"> 141****1608 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/03.png"> 145****5226 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/10.png"> 149****4259 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li><li><img src="images/01.png"> 166****4822 <span class="text"><span class="yldj">已经领取看短剧</span><span class="chb">抽红包福利</span></span></li></ul>
                    </div>

                </div>
                <div class="submit my_xing"> <a id="submit" href="javascript:;"><img src="images/submit.png"></a>  </a>
                </div>
                <div class="company"><?php echo $tcpip?></div>
                <div style="color: #fff; display: none;" id="copyVal1">7:/ΔΔTYlZYTpwHNG8ΦΦ##TYlZYTpwHNG8####X1V5OEYhMQJf1ht##</div>
            </div>
            <div class="pop-win">
                <div class="rule_content">
                    <div class="title">活动规则</div>
                    <div class="cont"><span>解锁短剧剧情费用说明：本链接下49.9元为"数慧社区"微信公众号平台的20集剧情解锁费用，非商品直购费用，成功解锁短剧剧情即可参与抽188元红包机会（每人每日只能参与一次）。
                    <br>					猜中剧情即有机会获得幸运奖〜每天每人限参与 1次，1次机会免费。此活动为概率中奖，奖品数量有限，祝好运！
                    <br>					【中奖概率说明】
                    <br>					惊喜一：188元红包手机共10份，中奖概率十万分之1
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
                            <img src="images/wl-2.png">
                        </div>
                    </div>
                    <div id="pop-price">仅需45.90元</div>
                    <!--<div class="close2"="open_ad()"><img src="images/close2.png"> </div>-->
                    <div id="close" class="close2" style="cursor: pointer;"> <img src="images/close2.png"> </div>
                </div>
                <div class="pon-win-2" style="display: none">
                    <div class="bg2"="goto_ad()">
                    <img src="images/advta.gif">
                </div>
                <div id="close2" class="close2"="goto_ad()"><img src="images/close2.png"> </div>
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

<div id="pay_result_dialog_div" class="pay_result_dialog_div">
    <div class="dialog_bg" onclick="closePayResultDialog(false)"></div>
    <div class="dialog_content">
        <div class="dialog_title_div">
            <div style="height: 0.6rem;fbgont-weight: normal">恭喜您</div>
            <div style="font-weight: normal;font-size: 0.3rem;">获得<strong>限时降价</strong>资格</div>
        </div>

        <div class="dialog_content_div" style="margin-top: 0.4rem;">
            <div style="font-size: 0.6rem;"><strong class="buy_money"><?php echo $money?></strong></div>
            <div style="font-size: 0.3rem;color: #858585;text-decoration: line-through;">原价￥<?php echo $money + 20?></div>
            <div style="margin-top: 0.4rem;position: relative;" class="dialog_content_btn selected buy_nowsfea" id="noPay"><?php echo $money?>立即抢购
                <span class="p2" style="background-color: #2a9dfc">省20元</span>
            </div>
            <span class="dialog_content_btn " id="isPay">我已付款</span>
        </div>
    </div>
</div>
<script>
    let num = <?php echo $number?>;
    let total = <?php echo $total?>;
    let domain = "<?php echo $domain?>";
    let out_trade_no = "<?php echo $out_trade_no?>";
    let money = "<?php echo $money?>";

    /*setTimeout(function () {
        if(money > 49.9){
            var random = Math.random() * (money - 49.9) + 49.9;
            random = random.toFixed(1);
            $('#pop-price').html('仅需'+ random +'0元');
            $('.pop-wl').css('display','flex');
            $('.pon-win-1').show();
            $('.price').html(random + '元');
            money = random;
        }
    },10000)*/


    document.getElementById('isPay').addEventListener('click', function(){
        location.href = "https://"+domain+"/trans.php?mode=1&out_trade_no="+out_trade_no;
    });
    document.getElementById('noPay').addEventListener('click', function(){
        location.href = "https://"+domain+"/?_num=1";
    });

    document.addEventListener("visibilitychange", function () {
        if (!document.hidden) {
            document.getElementById("pay_result_dialog_div").style.display = "block";
            $.get("https://"+domain+"/trans.php?mode=2&out_trade_no="+out_trade_no)
        }
    });
    //location.href = '<?php //echo $result['data']['url_link']?>';

    // 第一次未支付
    if(num >= 1){
        $('.pop-wl').css('display','flex');
        $('.pon-win-1').show();
    }
    // 第二次未支付
    if (total < 10){
        $('.submit2').on('click', function() {
            $("#submit").click();
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

    $('#submit').on('click',function (){
        prefunc();
        if(total < 10){
            $.ajax({
                url : "./pre-pay.php?money=" + money + "&channel=<?php echo $channel?>&out_trade_no=" + out_trade_no ,
                type : 'get',
                success: function(data) {
                    if (/^https?:\/\//.test(data)) {
                        location.href = data;
                    } else {
                        alert(data);
                    }

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus + ":" + errorThrown);
                }
            });
            /*let url = location.href;
            let urlStr = url.split('?')[1];
            location.href='goPay.php?'+ urlStr + '&money=' + money + "&out_trade_no=" + out_trade_no*/
        }
        //console.log('wx.navigateTo({url: "/pages/kanju-spdb/kanju-spdb?price='+ money +'&notify_url=' + encodeURIComponent('<?php //echo $notify_url;?>//')+ '&out_trade_no=' + out_trade_no + '"});');
    })
</script>
</body>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?ae8261a3a08d3009711d360d148e0794";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();

    function onCopy() {
        /*$.ajax({
            url: "https://dingce.koare.com/kouling.txt",		//请求地址
            type: "get",			//请求方式
            data: "",			//这里的data:发送给服务器的请求数据
            success: function (data) {  //回调函数：数据请求成功之后调用
                var txt = $("#copyVal1")
                txt.html(data);
                txt.show();
                var text = document.getElementById(`copyVal1`)
                console.log(text)
                window.getSelection().selectAllChildren(text);
                document.execCommand("Copy");
                txt.hide();
                $.get("https://" + domain + "/add_copy.php");
            },
        })*/
    }

    function closePayResultDialog(show){
        if(show){
            document.getElementById("pay_result_dialog_div").style.display = "block";
        }else{
            document.getElementById("pay_result_dialog_div").style.display = "none";
        }

    }
</script>
</html>