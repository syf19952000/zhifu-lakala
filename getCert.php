<?php
require("const.php");
require_once("inc/db.class.php");

$sql = "SELECT * FROM `mch`";
$query = $connect->query($sql);


while ($mch =  mysqli_fetch_assoc($query)){
    if(! ($mch['mch_cert'] || $mch['mch_pemkey'])){
        $cert = file_get_contents('svRefund/cert/' . $mch['mch_id'] . '/apiclient_cert.pem');
        $key = file_get_contents('svRefund/cert/' . $mch['mch_id'] . '/apiclient_key.pem');

        echo $mch['mch_id'] . "\t";

        $sql = "update mch set mch_cert='$cert', mch_pemkey='$key' where mch_id='$mch[mch_id]';";

        file_put_contents('cert.sql' , $sql . "\n", FILE_APPEND);

        $result = $connect->query($sql);

        echo ($result) . "\n";
    }
}