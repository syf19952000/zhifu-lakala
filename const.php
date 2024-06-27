<?php
require("utils.php");
// 全局统一配置
$domain = $_SERVER['HTTP_HOST'];
$date = date('H');
if($date % 3 == 0) {
    $money = 49.9;
}else if($date % 3 == 1){
    $money = 51.8;
}else{
    $money = 59.8;
}
$mch['data'] = [];
$mch['data']['mch_domain'] = $domain . '/zhifu-boqun';

// 拉卡拉公共参数
$lakala_appid = 'OP00000003';
$lakala_merchant_no = '82229007392000A';
$lakala_merchantPrivateKeyPath = 'D:/www/development/0619/guanmo.koare.com/public/lakala' . '/OP00000003_private_key.pem';

$require_js = '<script type="text/javascript">function prefunc(){}</script>';
if(isset($_GET['bxm_id'])){
    $notify_url = "https://" . $mch['data']['mch_domain'] . '/notify_bxmid.php';
    $clickidKey = 'bxm_id';
    $clickidVal = $_GET['bxm_id'];
}else if(isset($_GET['a_oId'])){
    $notify_url = "https://" . $mch['data']['mch_domain'] . '/notify_tuia.php';
    $clickidKey = 'a_oId';
    $clickidVal = $_GET['a_oId'];
}else if(isset($_GET['userkey'])){
    $notify_url = "https://" . $mch['data']['mch_domain'] . '/notify_xmob.php';
    $clickidKey = 'userkey';
    $clickidVal = json_encode([
        'userkey' => $_GET['userkey'],
        'md5phone' => $_GET['md5phone'] ?? '',
        'purchase_amount' => $_GET['purchase_amount'] ?? '',
        'plid' => $_GET['plid'] ?? '',
    ]);
    $require_js = '<script type="text/javascript" src="https://ss.qlink.ink/js/monitor.v5.min.js"></script> <script type="text/javascript">function prefunc(){Xmob_Monitor_5.xmob_active({type: 1});}</script>';
}

else if(isset($_GET['lakala_id'])){
    $notify_url = "https://" . $mch['data']['mch_domain'] . '/notify_lakala.php';
    $clickidKey = 'lakala_id';
    $clickidVal = $_GET['lakala_id'];
}


else{
    $notify_url = "https://" . $mch['data']['mch_domain'] . '/notify.php';
    $clickidKey = 'qcjParamStr';
    $clickidVal = $_GET['qcjParamStr'];
}

$tcpip = [
    'dingce.koare.com' => '北京数慧生活科技有限公司<br> 客服电话：01053396307 <br>京ICP备2022030573号-9',
    'shdj.yakeduanju.com.cn' => '哈尔滨雅可科技有限公司<br> 客服电话：01053396307 <br>黑ICP备2023008902号-1',
    'duanjt.ylhdad.com' => '北京数慧生活科技有限公司<br> 客服电话：01053396307 <br>京ICP备2022030573号-7',
];

$tcpip = isset($tcpip[$domain]) ? $tcpip[$domain] : $tcpip['dingce.koare.com'];

$attach_num = 4;

$merchantNo = '10089632887';

$config = json_decode('{
	"app_key": "app_10089632887",
	"server_root": "https://openapi.yeepay.com/yop-center",
	"yos_server_root": "https://yos.yeepay.com/yop-center",
	"yop_public_key": [{
		"store_type": "string",
		"cert_type": "RSA2048",
		"value": "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA6p0XWjscY+gsyqKRhw9MeLsEmhFdBRhT2emOck/F1Omw38ZWhJxh9kDfs5HzFJMrVozgU+SJFDONxs8UB0wMILKRmqfLcfClG9MyCNuJkkfm0HFQv1hRGdOvZPXj3Bckuwa7FrEXBRYUhK7vJ40afumspthmse6bs6mZxNn/mALZ2X07uznOrrc2rk41Y2HftduxZw6T4EmtWuN2x4CZ8gwSyPAW5ZzZJLQ6tZDojBK4GZTAGhnn3bg5bBsBlw2+FLkCQBuDsJVsFPiGh/b6K/+zGTvWyUcu+LUj2MejYQELDO3i2vQXVDk7lVi2/TcUYefvIcssnzsfCfjaorxsuwIDAQAB"
	}],
	"isv_private_key": [{
		"store_type": "string",
		"cert_type": "RSA2048",
		"value": "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDNebtUZB3L/nrnlm0ZuGAEHTGYwIK9kfSKK4Ii19PaEzEvWOFawp8Eql/xq7EsIhz713QczedVq49rMS8JlWoqK0hWATx7tA8UCRkw1RliJY93YhWeuVL7yO+6CMmmsFIc1ZMTiLduxYggQAG2U9N5+2xkYFojYJ3YSvN1EJuwenxAcj8ctmPpEOu8QPfa0DLlUxM3rbNmd/qkNd+eOmAjqnP14Eli2u0TXODvJ7bo5CXCrww8lu3niRxzU5tgzgZwAXuDmUO/ufjn6nutvzxYiu5WliUNM9kzRqy+tSMdB2PQJAmtvRaW594vUc5mQgCI98XWSvO7NcXG/SgsMV5dAgMBAAECggEAFwQUvL4+LDSENd6+t8vUTYGrpY2i13M67iMzf42neFz//Gq7S3xTbaVb++kcfnIPxxH6OqPBzvHZrcsjlrI64yBYCPI4gS7JJZhjTvn/zuOgL2I4Q8BEkFmQqMkFNXu4puwxflWpHmpX9izVREQdgyNrn86/KglPhQPYQ3upxNmv+Uo84opv/ynlr0/ekBTkq/+X51fyBEQY0XmhjN3lqgjNER12gkRsXWX6QI/8K28/fPN0VQxk+hdIyor7HGn0yPWFmi8R1RxC/Qe4K0sHyUWrm4z33NkYeRywiIRTkuJ7BHJu2zdTmdutXz3A1Uqu1ZXjjhPN3NgZ44oqsWY1LQKBgQD4DI0JzI8S/hkg2gvktGNvsZ8mmvzHrhLo6Y1Ot3+VhdMReV4MmFlkOc7aazqLE5iR7AAUBzjNJBemyR3yZwEGZ4Ldc2R+tbga5ozR4G6OhAw81qzocDF3kRTPLKuMAfZr7X6G9A2zF85APqZjuou7sFw/wnLCmeFvKljCGhneGwKBgQDUD9Rkv2LTLlh2+QeSBLjUDJnyk9a1zMGnNUozJXMt76vTRHMvg5PSFqyw0H+cWkAAomBkQt94PfEQDVNZRy5Jr+hL+79Rbhal2af4TMSF7NrdrPJKC7JNeE6c6M4y8bSYF73pMYBIODqrDhkEzmRrIH1aoJzgKw9niaJ4/J0c5wKBgE+vyIeDXdteIjh5lCSPBvrU9ynx1I9Zjt2gWjMVpDkm2t3cPaiSMPWP1LI6jH4td3A5fvTwNQFCPQ7pwWMkUCH8kwANmBVUz6ZktoyeKyZBqaTJ2pZ5pIqi4yqDUUETSFT34RP9x4Dnfu0Is2+KeGV9jMU29KMryE5sLkN9Gx5NAoGBAJDEjbaJgSmyBeqSbzybxKHeSGp1yqopsjo4AwOPmWXOrHp5zDivloZhvFqou7U9V1wXfb50eKJZhuvybOgR0+OyFt9oaV9pzEREpoq3vaG/5FpY6r7p5TtF2kT8GWhl4WfDLX64sF6dJFAKO8IwNBV+BJWN+AqA2u9P4n691XQZAoGBALnQ1m/IxIDGUDcGxcF21/0vEPS2coySJ09F5UcrHopQGSCU+XtCqIvgqXg/t6mtB1T2mkcJMDi1fw1grQ7zNb7eCmZAEPDZx/4GmzL+NX9AU9DvibM0g78EjCm84ss8b4/LuevPjsXVHbK3PLof/OTThlD9xvaaCE1SZ5+8yvJR"
	}],
	"http_client": {
		"connect_timeout": 10000,
		"read_timeout": 30000,
		"max_conn_total": 2000,
		"max_conn_per_route": 1000
	}
}', true);