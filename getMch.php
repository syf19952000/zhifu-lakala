<?php
require_once("inc/db.class.php");
require("const.php");

$data = ['mch_id' => '1650552575', 'mch_key' => 'yuechunlin152012725358888888liwE'];
$finalurl = "http://fb.ylhdad.com/web/index.php?store_id=1&r=api/index/set-mch";
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $finalurl);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$result = curl_exec($ch);
var_dump($result);die;