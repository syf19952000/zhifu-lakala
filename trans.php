<?php
    require("const.php");
    require_once("inc/db.class.php");
    // sleep(2);
    $mode = $_GET['mode'];
    $out_trade_no = $_GET['out_trade_no'];
    $sql = "SELECT * FROM `order` where out_trade_no = '$out_trade_no'";
    $result = mysqli_fetch_array($connect->query($sql));
    $myfile = fopen("log.txt", "a+") or die("Unable to open file!");
    fwrite($myfile, $sql."\r\n");
    if($mode == 1){
        // 手动触发模式
        $domain = $mch['data']['mch_domain'];
        $total = check_third_order($connect);   // 查询$_COOKIE['open_id']是否有值之后 存入cookie
        setcookie("open_id", $total + 1 , strtotime(date('Y-m-d', strtotime('+1 day'))));
        if ($result['zt'] == 1){
            header("Location: https://".$domain."/wancheng.html");
        }else{
            header("Location: https://".$domain."/?_num=1");
        }
    }
    fclose($myfile);