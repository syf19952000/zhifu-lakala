<?php
require("const.php");
require_once("inc/db.class.php");

$id = $_GET['id'];
$sql = "SELECT * FROM `order` where `transaction_id` = '$id'";
$order = mysqli_fetch_assoc($connect->query($sql));

//订单发货
$order_info = ['info' => [
    'order_key' => [
        'order_number_type' => 2,
        'transaction_id' => $order['transaction_id']
    ],
    'logistics_type' => 3,
    'delivery_mode' => 1,
    'shipping_list' => [array(
        'item_desc' => '看剧福利 '
    )],
    'upload_time' => date(''),
    'payer' => [
        'openid' => $order['openid']
    ]

]];
$finalurl = "http://fb.ylhdad.com/web/index.php?store_id=1&r=api/index/upload-shipping";
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $finalurl);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $order_info);
$data = curl_exec($ch);
curl_close($ch);
var_dump($data);