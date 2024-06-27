<?php
require_once("../const.php");
require_once("inc/db.class.php");

// require_once ("Yop-SDK/YopRsaClient.php");
// require_once ("Yop-SDK/Util/YopSignUtils.php");

$myfile = fopen("notify.txt", "a+") or die("Unable to open file!");
fwrite($myfile, "拉卡拉回调时间：".date('YmdHis')."\r\n");

$response = $_POST['response'];     // 或者 $test_xml = file_get_contents("php://input");
fwrite($myfile, $response."\r\n");

// $json_xml = YopSignUtils::decrypt($response,$config['isv_private_key'][0]['value'],$config['yop_public_key'][0]['value']);

fwrite($myfile, "$json_xml\r\n");

$json_xml = json_decode($json_xml, true);



if($return_code == "SUCCESS"){
    $req_info = $json_xml['req_info'];
    $mch_id = $json_xml['mch_id'];     
    $sql = "select * from `mch` where mch_id='$mch_id'";
    $mch = mysqli_fetch_assoc($connect->query($sql));
    //解密
    $decrypted = openssl_decrypt(base64_decode($req_info), 'AES-256-ECB', md5($mch['mch_key']), OPENSSL_RAW_DATA);

    $json_xml = json_encode(simplexml_load_string($decrypted, 'SimpleXMLElement', LIBXML_NOCDATA));
    file_put_contents('log.txt', $json_xml . "\n", FILE_APPEND);
    $json_xml = json_decode($json_xml, true);

    $refund_status = $json_xml['refund_status'];
    $transaction_id = $json_xml['transaction_id'];
    $refund_id = $json_xml['refund_id'];
    $out_refund_no = $json_xml['out_refund_no'];
    $total_fee = $json_xml['total_fee'];
    $refund_fee = $json_xml['refund_fee'];
    $success_time= $json_xml['success_time'];
    $sql = "SELECT * from `order` where transaction_id = '$transaction_id'";
    $order = mysqli_fetch_array($connect->query($sql));

    $sql = "SELECT * from `refund` where out_refund_no = '$out_refund_no'";
    $result = mysqli_fetch_array($connect->query($sql));
    if ($refund_status == 'SUCCESS'){
        if($order){
            $sql = "UPDATE `order` SET refund = 1 where transaction_id = '$transaction_id' ";
            $connect->query($sql);

            if($result){
                $sql = "UPDATE `refund` SET `status`=1, refund_id='$refund_id', out_refund_no='$out_refund_no', total_fee='$total_fee', refund_fee='$refund_fee', create_time='$success_time' where out_refund_no = '$out_refund_no'";
                $connect->query($sql);
                file_put_contents('log.txt', "api refund \n", FILE_APPEND);
            }else{
                $sql = "INSERT INTO `refund` (transaction_id, refund_id, out_trade_no, out_refund_no, mch_id, refund_fee, total_fee, status, create_time) VALUES ('$transaction_id', '$refund_id', '$order[out_trade_no]', '$out_refund_no', '$order[mch_id]', '$refund_fee', '$total_fee', 1, '$success_time')";
                $connect->query($sql);
                file_put_contents('log.txt', "WeChat refund \n", FILE_APPEND);
            }
        }
    }elseif($refund_status == 'CHANGE'){
        if($order){
            if($result){
                $sql = "UPDATE `refund` SET `status`=2, refund_id='$refund_id', out_refund_no='$out_refund_no', total_fee='$total_fee', refund_fee='$refund_fee', create_time='$success_time' where out_refund_no = '$out_refund_no'";
                $connect->query($sql);
                file_put_contents('log.txt', "api refund change \n", FILE_APPEND);
            }else{
                $sql = "INSERT INTO `refund` (transaction_id, refund_id, out_trade_no, out_refund_no, mch_id, refund_fee, total_fee, status, create_time) VALUES ('$transaction_id', '$refund_id', '$order[out_trade_no]', '$out_refund_no', '$order[mch_id]', '$refund_fee', '$total_fee', 2, '$success_time')";
                $connect->query($sql);
                file_put_contents('log.txt', "WeChat refund change \n", FILE_APPEND);
            }
        }
    }
}




fwrite($myfile, "拉卡拉退款回调 end \n");
fclose($myfile);

echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';