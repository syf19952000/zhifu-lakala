<?php
/**
 * 微信 H5 支付 PHP 版本 demo 部分代码来自网络
 * 作者：沈唁 
 * 博客：https://qq52o.me
 */
$money= 1;                     //充值金额 微信支付单位为分
$userip = get_client_ip();     //获得用户设备 IP
$appid  = "wx148eb37c8bd02625";                  //应用 APPID
$mch_id = "1515935051";                  //微信支付商户号
$key    = "LWshuhuishenghuoSHE25qeudxlsfkHJ";                 //微信商户 API 密钥
$out_trade_no = date('Ymdhis').rand(1000,9999);//平台内部订单号
$nonce_str = createNoncestr();//随机字符串
$body = "H5 充值";//内容
$total_fee = $money; //金额
$spbill_create_ip = $userip; //IP
$notify_url = "http://www.ylhdad.com.cn/zhifu/notify.php"; //回调地址
$trade_type = 'MWEB';//交易类型 具体看 API 里面有详细介绍
$scene_info ='{"h5_info":{"type":"Wap","wap_url":"http://qq52o.me","wap_name":"支付"}}';//场景信息 必要参数
$signA ="appid=$appid&attach=$out_trade_no&body=$body&mch_id=$mch_id&nonce_str=$nonce_str&notify_url=$notify_url&out_trade_no=$out_trade_no&scene_info=$scene_info&spbill_create_ip=$spbill_create_ip&total_fee=$total_fee&trade_type=$trade_type";
$strSignTmp = $signA."&key=$key"; //拼接字符串  注意顺序微信有个测试网址 顺序按照他的来 直接点下面的校正测试 包括下面 XML  是否正确
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
                    <attach>$out_trade_no</attach>
                    <nonce_str>$nonce_str</nonce_str>
                    <sign>$sign</sign>
            </xml>";//拼接成 XML 格式
$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";//微信传参地址
$dataxml = postXmlCurl($post_data,$url); //后台 POST 微信传参地址  同时取得微信返回的参数 
$objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的 XML 转换成数组
//var_dump($objectxml);exit;
function createNoncestr( $length = 32 ){
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $str ="";
    for ( $i = 0; $i < $length; $i++ )  {
        $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
    }
    return $str;
}
function postXmlCurl($xml,$url,$second = 30){
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
    //设置 header
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //post 提交方式
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    //运行 curl
    $data = curl_exec($ch);
    //返回结果
    if($data){
        curl_close($ch);
        return $data;
    }else{
        $error = curl_errno($ch);
        curl_close($ch);
        echo "curl 出错，错误码:$error"."<br>";
    }
}
function get_client_ip($type = 0) {
    $type       =  $type ? 1 : 0;
    $ip         =   'unknown';
    if ($ip !== 'unknown') return $ip[$type];
  //  if($_SERVER['HTTP_X_REAL_IP']){//nginx 代理模式下，获取客户端真实 IP
   //     $ip=$_SERVER['HTTP_X_REAL_IP'];
  //  }else
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的 ip
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的 ip 地址
    }else{
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    // IP 地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}
?>
<!DOCTYPE html>
<html style="font-size: 85.2px;" lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <title>看剧福利</title>
    <!-- 引入样式文件 -->
    <link rel="stylesheet" href="images/vantIndex.css">

    <!-- 引入 Vue 和 Vant 的 JS 文件 -->
    <script src="images/vue.min.js"></script>
    <script src="images/vant.min.js"></script>
    <!--    axios-->
    <script src="images/axios.min.js"></script>
    <!-- vue-cookie -->	
    <script src="images/vue-cookies.js"></script>
	<script src="http://libs.baidu.com/jquery/2.1.4/jquery.min.js"></script>
<script>
    var htmlWidth = document.documentElement.clientWidth;
    document.documentElement.style.fontSize = htmlWidth/20+'px';
</script><style>
    body{
        width: 20rem;
        height: 100%;
        background-color: #c04227;
    }
    .blockSpan{
        display: inline-block;
        width: 1rem;
        text-align: center;
    }
    @keyframes scaleDraw {  /*定义关键帧、scaleDrew是需要绑定到选择器的关键帧名称*/
        0%{
            transform: scale(1);  /*开始为原始大小*/
        }
        25%{
            transform: scale(1.2); /*放大1.1倍*/
        }
        50%{
            transform: scale(1);
        }
        75%{
            transform: scale(1.2);
        }
    }

</style></head>


<body>
<div id="app"><div style="width: 20rem;"><div style="position: absolute; top: 9rem; right: 3rem;"><img src="images/price3.png" style="width: 3rem;"></div> <img src="images/bg.png" style="width: 20rem;"></div> <div style="width: 20rem; font-size: 0.85rem; line-height: 2rem;">
<div style="width: 20rem;"><div style="position: absolute; top: 27rem; background: linear-gradient(0deg, rgb(247, 26, 30), rgb(255, 102, 48)); width: 14rem; margin-left: 3rem; font-size: 1.3rem; line-height: 3rem; color: white; text-align: center; border-radius: 1.5rem; animation: 2s ease-in-out 0s infinite normal none running scaleDraw;">
                <a href="<?php echo $objectxml['mweb_url'] ?>" class="click_pop">确认领取</a>
            </div></div></div> <div style="margin-top: 3rem; text-align: center; width: 20rem; font-size: 0.65rem;">
        北京数慧生活科技有限公司<br>
        京ICP备2022030573号<br>
        客服热线:010-59453521
    </div> <!----> <div style="position: absolute; right: 0px; top: 2rem; width: 1rem;"><img src="images/ruleBtn.png" style="width: 1rem;"> <img src="images/serviceBtn.png" style="width: 1rem; margin-top: 0.5rem;"> <a href="tuikuan.html"><img src="images/refundBtn.png" style="width: 1rem; margin-top: 0.5rem;"></a></div> <div class="van-overlay" style="display: none;"><div style="width: 20rem; display: flex; align-items: center; justify-content: center; height: 100%;"><div style="width: 19rem; background-color: white; text-align: center; padding-top: 1rem; padding-bottom: 1rem; border-radius: 0.3rem;"><span style="font-size: 1.2rem; font-weight: bolder; line-height: 2rem;">恭喜你</span> <br> <span style="font-size: 0.85rem;">获得<span style="color: rgb(237, 91, 66);">限时降价</span>资格</span> <br> <br> <span style="color: rgb(237, 91, 66); font-size: 2rem;"><span style="font-size: 0.85rem;">¥</span>27.9</span> <br> <span style="font-size: 0.65rem; color: rgb(180, 176, 173); text-decoration: line-through;">原价¥29.9</span> <div style="width: 80%; margin-left: 10%; background-color: rgb(66, 139, 244); border-radius: 0.85rem; font-size: 1rem; line-height: 2rem; color: white; margin-top: 1rem;">
                    27.9立即抢购
                </div> <div style="position: relative; top: -2.5rem; left: 13rem;"><div style="background-color: red; position: absolute; padding: 0px 0.3rem; border-radius: 0.4rem 0.4rem 0.4rem 0px; font-size: 0.45rem; color: white;">
                        立减2元
                    </div></div> <div style="width: 80%; margin-left: 10%; background-color: red; border-radius: 0.85rem; font-size: 1rem; line-height: 2rem; color: white; margin-top: 1rem;">
                    我已付款
                </div></div></div></div></div>


<!--遮罩 -->
<style type="text/css">
/*遮罩*/
        .pop {    width: 50%; height: 160px; position: absolute;  top: 0;  left: 0;  bottom: 0;  right: 0;  margin: auto;   z-index: 130;  border-radius: 8px;  background-color: #fff;  box-shadow: 0 3px 18px rgba(100, 0, 0, .5);  }
        .pop-top{  height:40px;  width:100%;  border-bottom: 1px #E5E5E5 solid;  }
        .pop-top h2{  float: left;  display:black}
        .pop-top span{  float: right;  cursor: pointer;  font-weight: bold; display:black}
        .pop-foot{  height:50px;  line-height:50px;  width:100%;  border-top: 1px #E5E5E5 solid;  text-align: right;  }
        .pop-cancel, .pop-ok {  padding:8px 15px;  margin:15px 5px;  border: none;  border-radius: 5px;  background-color: #337AB7;  color: #fff;  cursor:pointer;  }
        .pop-cancel {  background-color: #FFF;  border:1px #CECECE solid;  color: #000;  }
        .pop-content{  height: 220px;  }
        .pop-content-left{  float: left;  }
        .pop-content-right{  width:100%;  float: left;  padding-top:20px;  padding-left:0px;  font-size: 16px;  line-height:35px;  }
        .bgPop{    position: absolute;  z-index: 129;  left: 0;  top: 0;  width: 100%;  height: 100%;  background: rgba(0,0,0,.2);  }
		
.table_css {
	width:100%;
}
.td_css {
    text-align: center;
    border-top: 1px solid #CCCCCC;
    border-bottom: 1px solid #CCCCCC;
	height:50px;
	line-height:50px;
	font-weight:600;
	background-color: #f8f8f8;
}
.td_css1 {
    text-align: center;
	height:50px;
	line-height:50px;
}
.weekdayprice{width:34px;}
</style>
<!--遮罩层-->
<div class="bgPop"  style=" display:none"></div>
<!--弹出框-->
            <form action="?id=<?php echo $id;?>&b=<?php echo $b;?>" name="form1" method="post" class="form-search" onsubmit="return check7tian();">
<div class="pop"  style=" display:">
    <div class="pop-content">
       	<table border="0" cellspacing="0" class="table_css">
                <tbody>
				<tr>
                    <td class="td_css1">。。。。。。。</td>
                </tr>
				<tr>
                    <td class="td_css"><a href="#" style="color:red;">已完成支付</a></td>
                </tr>
				<tr>
                    <td class="td_css1"><a href="tel:010-66666666" style="color:#666;">联系客服寻求帮助</a></td>
                </tr>                     
            	</tbody>
          </table>
      </div>
    </div>
    <div class="pop-foot">
        <input value="关闭" class="pop-cancel pop-close" type="button">
        <input name="提交" type="submit" class="pop-ok" value="确定">
    </div>
    </div>
            </form>
<script>
    $(document).ready(function () {
        $('.pop-close').click(function () {
            $('.bgPop,.pop').hide();
        });
        $('.click_pop').click(function () {
            $('.bgPop,.pop').show();
        });
    })

</script>
<!--遮罩 结束-->

</body></html>