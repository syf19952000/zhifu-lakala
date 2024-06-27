<?php
$myfile = fopen("log.txt", "a+") or die("Unable to open file!");
fwrite($myfile, "回调时间：".date('YmdHis')."\r\n");

$response = $_POST;
fwrite($myfile, json_encode($response)."\r\n");

require_once("const.php");
require_once("inc/db.class.php");

$myfile = fopen("log.txt", "a+") or die("Unable to open file!");
fwrite($myfile, "回调时间：".date('YmdHis')."\r\n");

// 回调数据
$mch_id = $response['appid'];
$open_id = '';
$money = $response['amount'];
$nonce_str = json_encode($response);
$out_trade_no = $response['mchntOrderNo'];
$transaction_id = $response['orderNo'];
$resultNo = $response['paySt'];
// 上报第一次支付记录
$sql = "SELECT * from `order` where out_trade_no = '$out_trade_no'";
$result = mysqli_fetch_array($connect->query($sql));
if ((!isset($result['out_trade_no'])) && $resultNo == '2'){
    $sql = "SELECT * FROM `history` where out_trade_no = '$out_trade_no' ";
    $result = mysqli_fetch_array($connect->query($sql));
    $qcj = $result['qcjParamStr'];

    $result = '';
    $sql = "INSERT INTO `order` (out_trade_no, transaction_id, money, bxmid, nonce_str, mch_id, `result`, open_id, zt, `source`) VALUES ('$out_trade_no', '$transaction_id', $money, '$qcj', '$nonce_str', '$mch_id', '', '$open_id', 1, 'boqun')";
    fwrite($myfile, "INSERT order " . $sql . "\r\n");
    $result = $connect->query($sql);
    fwrite($myfile, "INSERT order result " . $result . "\r\n");

    $result = null;
    // 上报记录扣量
    $control = json_decode(file_get_contents('control.json'), true);

    $attach = $control['bxmid'];

    // 二次拦截不参与计数
    if ($money != 45.90) {
        $control['bxmid'] += 1;
    }

    if ($control['bxmid'] > $attach_num) {
        $control['bxmid'] = 1;
    }
    file_put_contents('control.json', json_encode((object)$control));

    if ($attach < $attach_num && $money != 45.90) {
        //if($control[$attach] <= 3 && $money != 4590){
        $data['mt'] = 8;
        $data['bxmid'] = $qcj;
        $data['conversion_type'] = 5;
        $finalurl = "https://openlog.bianxianmao.com/inads/s.gif?" . http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $finalurl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $result = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        fwrite($myfile, '变现猫请求结果:' . json_encode($result) . json_encode($error)."\r\n");
        // 返回结果
        $sql = "UPDATE `order` set result = '$result' where out_trade_no = '$out_trade_no' ";
        $connect->query($sql);
    }else{
        fwrite($myfile, '变现猫略过');
    }

    $sql = "SELECT COUNT(*) as total from history";
    $result = mysqli_fetch_array($connect->query($sql));
    $total = $result['total'];
    if ($total >= 3500) {
        $sql = "delete from history order by id limit 900";
        $connect->query($sql);
    }
}

fclose($myfile);
echo '{"success":"true"}';