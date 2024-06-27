<?php
header("content-type:text/html;charset=utf-8");
require("const.php");
require_once("inc/db.class.php");

function getSign($params, $key)
{
    // 添加签名
    ksort($params, SORT_STRING);
    $unSignParaString = formatQueryParaMap($params, false);
    $signStr = strtoupper(md5($unSignParaString . "&key=" . $key));
    return $signStr;
}

function formatQueryParaMap($paraMap, $urlEncode = false)
{
    $buff = "";
    ksort($paraMap);
    foreach ($paraMap as $k => $v) {
        if (null != $v && "null" != $v) {
            if ($urlEncode) {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
    }
    $reqPar = '';
    if (strlen($buff) > 0) {
        $reqPar = substr($buff, 0, strlen($buff) - 1);
    }
    return $reqPar;
}

function arrayToXml($arr)
{
    $xml = "<xml>";
    foreach ($arr as $key => $val) {
        if (is_numeric($val)) {
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
        } else
            $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
    }
    $xml .= "</xml>";
    return $xml;
}

$id = $_GET['id'];
$sql = "SELECT * FROM `order` where `transaction_id` = '$id'";
$refund = mysqli_fetch_assoc($connect->query($sql));

$sql = "select * from `mch` where mch_id='$refund[mch_id]'";
$mch = mysqli_fetch_assoc($connect->query($sql));

$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
$nonceStr = '';
for ($i = 0; $i < 16; $i++) {
    $nonceStr .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
}

$unified = array(
    'appid' => $appid,
    'mch_id' => $mch['mch_id'],
    'nonce_str' => $nonceStr,
    'transaction_id' => $id
);
$unified['sign'] = getSign($unified, $mch['mch_key']);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.mch.weixin.qq.com/pay/refundquery');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, arrayToXml($unified));
curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$data = curl_exec($ch);
curl_close($ch);

$unifiedOrder = json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA));

$unifiedOrder = json_decode($unifiedOrder, true);

print_r($unifiedOrder);die;