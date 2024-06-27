<?php
function postXmlCurl($xml,$url,$out_trade_no, $domain){
    /*
     * xml数据发送post请求
     */
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
    //输出 header
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    // curl_setopt($ch, CURLOPT_REFERER, 'https://www.wtaic.com');
    //post 提交方式
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    //运行 curl
    $data = curl_exec($ch);
    //返回结果
    if($data){
        curl_close($ch);
        return $data;
    }else{
        $error = curl_errno($ch);
        curl_close($ch);
        echo "curl 出错，错误码:$error"."<br>";
    }
}

function check_third_order($connect){
    /**
     * 限制三次订单支付
     */
    $start = date('Y-m-d');
    $end = date('Y-m-d H:i:s');
    $openid = $_COOKIE['open_id'];
    /*$sql = "SELECT COUNT(*) as total FROM (SELECT *, STR_TO_DATE(SUBSTRING(out_trade_no, 1, 14), '%Y%m%d%H%i%s') as time FROM `order`) as heihei where heihei.open_id = '$openid' and heihei.time between '$start' and '$end' ";
    // $sql = "SELECT COUNT(*) as total FROM (SELECT *, STR_TO_DATE(SUBSTRING(out_trade_no, 1, 14), '%Y%m%d%H%i%s') as time FROM `order`) as heihei where heihei.open_id = '$openid' and heihei.time between '2023-8-18' and '2023-8-19' ";
    $result = mysqli_fetch_array($connect->query($sql));*/
    // 限制支付白名单
    /*if($openid == 'odpW6wIHrhvpeV1Hs2qJjQD8kI94' || $openid == 'odpW6wB-d5qfURSxwL4IqqwUZgVw'){
        $result['total'] = 1;
    }*/
    return empty($openid) ? 0 : $openid;
}

function get_client_ip($type = 0) {
    /*
     * 获取用户ip
     */
    $type       =  $type ? 1 : 0;
    $ip         =   'unknown';
    if ($ip !== 'unknown') return $ip[$type];
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的 ip
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的 ip 地址
    }else{
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    // IP 地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

function createNoncestr($length = 32){
    /*
     * 微信支付Noncestr
     */
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $str ="";
    for ($i = 0; $i < $length; $i++ )  {
        $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
    }
    return $str;
}

function get_with_data($url){
    /*
     * get请求发送数据
     */
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    $result = curl_exec($curl);
    if ($result){
        curl_close($curl);
    }
    return $result;
}

function check_mch_valuable($connect, $out_trade_no, $mch){
    $myfile = fopen("log.txt", "a+") or die("Unable to open file!");
    $sql = "SELECT * FROM `order` where out_trade_no = '$out_trade_no'";
    $result = mysqli_fetch_array($connect->query($sql));
    fwrite($myfile, $sql.'\r\n');
    $mch_id = $mch['data']['mch_id'];
    $sql = "SELECT * FROM `mch` where mch_id = '$mch_id'";
    fwrite($myfile, $sql.'\r\n');
    $mch_result = mysqli_fetch_array($connect->query($sql));
    $mch_number = $mch_result['mch_number'];
    $mch_error = $mch_result['mch_error'];
    if (!$result){
        // 如果没有支付异常次数增加
        $mch_number++;
        if ($mch_number == 60){
            // 异常等级调整为下线
            $mch_error = 2;
        }
    }else{
        // 如果有支付重置异常次数
        $mch_number = 0;
    }
    // 更新结果
    $sql = "UPDATE `mch` SET mch_error = '$mch_error', mch_number = '$mch_number' whrere mch_id = '$mch_id'";
    fwrite($myfile, $sql."\r\n");
    $connect->query($sql);
    fclose($myfile);
}

function postUrl($url, $data){
    $headers = array('Content-Type: application/x-www-form-urlencoded');
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_HEADER, FALSE);//不抓取头部信息。只返回数据
    curl_setopt($curl, CURLOPT_TIMEOUT,30);//超时设置
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);//1表示不返回bool值
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($curl); // 执行操作

    if (curl_errno($curl)) {
        echo 'Errno'.curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $result;
}
