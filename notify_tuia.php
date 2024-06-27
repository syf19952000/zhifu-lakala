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
$mch_id = '';
$open_id = '';
$money = $response['pay_money'];
$nonce_str = json_encode($response);
$out_trade_no = $response['pay_order_sn'];
$transaction_id = $response['order_id'];
// 上报第一次支付记录
$sql = "SELECT * from `order` where out_trade_no = '$out_trade_no'";
$result = mysqli_fetch_array($connect->query($sql));
if (!isset($result['out_trade_no'])){
    $sql = "SELECT * FROM `history` where out_trade_no = '$out_trade_no' ";
    $result = mysqli_fetch_array($connect->query($sql));
    $qcj = $result['qcjParamStr'];

    $result = '';
    $sql = "INSERT INTO `order` (out_trade_no, transaction_id, money, tuia, nonce_str, mch_id, `result`, open_id, zt, `source`) VALUES ('$out_trade_no', '$transaction_id', $money, '$qcj', '$nonce_str', '$mch_id', '', '$open_id', 1, 'fugui')";
    fwrite($myfile,"INSERT order ".$sql."\r\n");
    $result = $connect->query($sql);
    fwrite($myfile, "INSERT order result ".$result."\r\n");
    fclose($myfile);

    $result = null;
    // 上报记录扣量
    $control = json_decode(file_get_contents('control.json'), true);

    $attach = $control['tuia'];

    // 二次拦截不参与计数
    if($money != 45.90){
        $control['tuia'] += 1;
    }

    if ($control['tuia'] > $attach_num){
        $control['tuia'] = 1;
    }
    file_put_contents('control.json', json_encode((object) $control));

    if ($attach < $attach_num && $money != 45.90){
        //if($control[$attach] <= 3 && $money != 4590){
        $data['advertKey'] = "94E68F40460E5B097C1F373468E28FCE";
        $data['a_oId'] = $qcj;
        $data['subType'] = 3;

        $finalurl = "https://activity.tuia.cn/log/effect/v2?" . http_build_query($data);
        fwrite($myfile,"Send tuia ".$finalurl."\r\n");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $finalurl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        // 返回结果
        if($result){
            curl_close($ch);
            $sql = "UPDATE `order` set result = '$result' where out_trade_no = '$out_trade_no' ";
            $connect->query($sql);
        }
    }

    $sql = "SELECT COUNT(*) as total from history";
    $result = mysqli_fetch_array($connect->query($sql));
    $total = $result['total'];
    if ($total >= 3500){
        $sql = "delete from history order by id limit 900";
        $connect->query($sql);
    }
}

echo 'success';