<?php
$myfile = fopen("log.txt", "a+") or die("Unable to open file!");
fwrite($myfile, "回调时间：".date('YmdHis')."\r\n");

$response = $_POST;
fwrite($myfile, json_encode($response)."\r\n");

require_once("const.php");
require_once("inc/db.class.php");

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

echo '打印订单状态';
var_dump($result);die;

if ((!isset($result['out_trade_no'])) && $resultNo == '2'){
    $sql = "SELECT * FROM `history` where out_trade_no = '$out_trade_no' ";
    $result = mysqli_fetch_array($connect->query($sql));
    $qcj = $result['qcjParamStr'];   // 这是什么

    $result = '';
    // $sql = "INSERT INTO `order` (out_trade_no, transaction_id, money, qcjParamStr, nonce_str, mch_id, `result`, open_id, zt, `source`) VALUES ('$out_trade_no', '$transaction_id',
    //      $money, '$qcj', '$nonce_str', '$mch_id', '', '$open_id', 1, 'boqun')";

    $sql = "INSERT INTO `order` (out_trade_no, transaction_id, money, lakala, nonce_str, mch_id, `result`, open_id, zt, `source`) VALUES ('$out_trade_no', '$transaction_id', $money, '$qcj', '$nonce_str', '$mch_id', '', '$open_id', 1, 'lakala')";
        

    fwrite($myfile,"INSERT order ".$sql."\r\n");
    $result = $connect->query($sql);
    fwrite($myfile, "INSERT order result ".$result."\r\n");

    $result = null;
    // 上报记录扣量
    $control = json_decode(file_get_contents('control.json'), true);

    $attach = $control['attach'];

    // 二次拦截不参与计数
    if($money != 45.90){
        $control['attach'] += 1;
    }

    //    $control['attach'] = 1 在 control.json中设置
    //    $attach_num 在 const.php 中设置 $attach_num = 2;
    if ($control['attach'] > $attach_num){
        $control['attach'] = 1;
    }
    file_put_contents('control.json', json_encode((object) $control));

    if ($attach < $attach_num && $money != 45.90){
        //if($control[$attach] <= 3 && $money != 4590){


        // $data['event'] = 2;
        // $data['qcjParamStr'] = $qcj;
        // $data = json_encode($data);
        // $finalurl = "https://dsp-api.fitnesstip.cn/qcj/api/third/landPage/log";
        // $ch = curl_init();
        // curl_setopt($ch,CURLOPT_URL, $finalurl);
        // curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        // curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //         'Content-Type: application/json; charset=utf-8',
        //         'Content-Length: ' . strlen($data)
        //     )
        // );
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // curl_setopt($ch, CURLOPT_POST, TRUE);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // $result = curl_exec($ch);
        // $info = curl_getinfo($ch);
        // $error = curl_error($ch);
        // fwrite($myfile, '高阳请求结果:' . $result . json_encode($error)."\r\n");
        // // 返回结果
        // if($result){
        //     curl_close($ch);
        //     $sql = "UPDATE `order` set result = '$result' where out_trade_no = '$out_trade_no' ";
        //     $connect->query($sql);
        // }

    }else{
        // fwrite($myfile, '高阳略过');
        fwrite($myfile, '拉卡拉略过');
    }

    $sql = "SELECT COUNT(*) as total from history";
    $result = mysqli_fetch_array($connect->query($sql));
    $total = $result['total'];
    if ($total >= 3500){
        $sql = "delete from history order by id limit 900";
        $connect->query($sql);
    }
}

fclose($myfile);

echo '{"success":"true"}';
