<?php

require_once("const.php");
require_once("inc/db.class.php");
# 微信支付
$clickid='';
if (count($_GET)>0){
    $clickid = $clickidVal;
}
$userip = get_client_ip();     //获得用户设备 IP

$domain = $mch['data']['mch_domain'];

$out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : date('YmdHis').rand(1000,9999);//平台内部订单号

$money = isset($_GET['money']) ? $_GET['money'] : $money;

$spbill_create_ip = $userip; //IP

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
}
else if(isset($_GET['lakala_id'])){
    $channel = '_lakala';
}
else{
    $channel = '';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="images/jquery.min.js"></script>
    <style>
        *{ padding:0; margin:0;}/*去掉所有标签之间的间隙*/
        ul,li,ol{ list-style:none;}/*去掉列表的项目符号*/
        a{ text-decoration:none;}/*去掉超链接的下划线*/
        img{ border:none;}/*去掉图片加完链接之后的边框*/
        img,input,textarea,select{ vertical-align:middle;}/*设置表单元素以及图片垂直居中对齐*/
        html{font-size:12px;font-family: "微软雅黑";}
        body{margin:0 auto; width: 100%; height: 100%;}/*设置body中所有大div宽度为1002px,并且自动居中*/
        .container{
            width: 82%;
            margin:0 auto;
            text-align: center;
            padding-top: 8rem;
        }

        .price{
            font-size: 1.4444rem;
            color: #888888;
        }
        .price .money{
            font-size: 4rem;
            color: #1c1c1e;
            line-height: 8rem;
            font-weight: bold;
        }
        .msg{
            height: 5.91rem;
            line-height: 5.91rem;
            text-align: left;
            font-size: 1.4444rem;
            color: #888888;
            border-top: 1px solid #dcdcdc;
            border-bottom: 1px solid #dcdcdc;
        }
        .msg span{
            float: right;
            color: #1c1c1e;
        }
        .button{
            margin-top: 4.5rem;
        }
        .pay{
            height: 4.7rem;
            line-height: 4.7rem;
            background-color: #1aac1b;
            color: #FFFFFF;
            font-size: 1.65rem;
            box-shadow: 0 0 0 5px;
            border-radius: 5px;
        }
        .cancel{
            margin-top: 2.28rem;
            font-size: 1.444rem;
            color: #5d6991;
        }
        .gif
        {
            position:absolute;/*声明绝对定位*/
            left:0;/*在页面最左侧对齐*/
            top:0;/*在页面最上方对齐*/
            height:100%;/*高度是相对屏幕高度的100%*/
            width:100%;/*宽度是相对屏幕宽度的100%*/
            z-index:1;/*设置元素优先级为1级*/
            display:none;/*设置元素隐藏*/
            align-items: center;/*让子元素横向居中(声明flex后有效)*/
            justify-content: center;/*让子元素垂直居中(声明flex后有效)*/
            background-color:rgba(0,0,0,0.5);/*设置背景颜色为黑色，并给0.5透明度*/
        }
        .gif>img/*设置gif下的img元素样式*/
        {
            height:5%;
            width:11%;
        }
    </style>
    <title>支付</title>
</head>
<body>

<div class="container">

    <div class="price">
        数慧短剧
        <br/>
        <span class="money">￥<?php echo $money?></span>
    </div>
    <div class="msg">
        服务提供方 <span>北京数慧生活</span>
    </div>

    <div class="button">
        <div class="pay" id="submit">立即支付</div>
        <div class="cancel" id="cancel">取消</div>
    </div>

</div>
<div class = "gif">
    <img src = "images/loading.gif"/>
</div>
</body>
<script>
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


    /*document.getElementById('isPay').addEventListener('click', function(){
        location.href = "https://"+domain+"/trans.php?mode=1&out_trade_no="+out_trade_no;
    });*/
    document.getElementById('cancel').addEventListener('click', function(){
        location.href = "https://"+domain+"/?_num=1";
    });

    document.addEventListener("visibilitychange", function () {
        if (!document.hidden) {
            document.getElementById("pay_result_dialog_div").style.display = "block";
            $.get("https://"+domain+"/trans.php?mode=2&out_trade_no="+out_trade_no)
        }
    });

    // 第一次未支付
    // 第二次未支付
    if (total < 10){
        $('.submit2').on('click', function() {
            $("#submit").click();
        })
    }else{
        alert("活动仅限参加10次哦！")
    }

    $('#close').on('click',function (){
        $('.pon-win-1').hide();
        $('.pon-win-2').show();
    })
    $('.pon-win-2').on('click',function (){
        location.href = 'https://u.tuiabcedc.cn/2c84';
    })

    $('#submit').on('click',function (){
        if(total < 10){
            $(".gif").css("display","flex");
            $.ajax({
                url : "./pre-pay.php?money=" + money + "&channel=<?php echo $channel?>&out_trade_no=" + out_trade_no ,
                type : 'get',
                success: function(data) {
                    $(".gif").css("display","none");
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
        }
        //console.log('wx.navigateTo({url: "/pages/kanju-spdb/kanju-spdb?price='+ money +'&notify_url=' + encodeURIComponent('<?php //echo $notify_url;?>//')+ '&out_trade_no=' + out_trade_no + '"});');
    })
</script>
</html>
