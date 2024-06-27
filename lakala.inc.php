<?php
class Lakala
{
    // public $md5key = "827s%s3sj7sjk2!@ks";
    // public $error;


    private $appid;
    private $mchSerialNo;
    private $mercId;
    private $merchantPrivateKeyPath;
    private $lklCertificatePath;
    private $subject;
    private $schema = 'LKLAPI-SHA256withRSA';
    private $version = '3.0';
    private $outOrderNo;
    private $amount;
    private $notifyUrl;
    // private $uoUrl = 'https://test.wsmsd.cn/sit/api/v3/ccss/counter/order/create';
    private $uoUrl = 'https://test.wsmsd.cn/sit/api/v3/ips/trans/cqp_mp/order';   // 云微小程序/消费下单接口

    private $termno = 'D9296400';
    private $mer_wx_mp_app_id = 'wx63100f6a92712d70';
    private $invoke_scene = '04';
    
    
 
    /**
     * 设置APPId
     */
    public function setAppid($appid) {
        $this->appid = $appid;
    }
 
    /**
     * 设置mchSerialNo
     */
    public function setMchSerialNo($mchSerialNo) {
        $this->mchSerialNo = $mchSerialNo;
    }
 
    /**
     * 设置mercId
     */
    public function setMercId($mercId) {
        $this->mercId = $mercId;
    }
 
    /**
     * 设置merchantPrivateKeyPath
     */
    public function setMerchantPrivateKeyPath($merchantPrivateKeyPath) {
        $this->merchantPrivateKeyPath = $merchantPrivateKeyPath;
    }
 
    /**
     * 设置lklCertificatePath
     */
    public function setLklCertificatePath($lklCertificatePath) {
        $this->lklCertificatePath = $lklCertificatePath;
    }
 
    /**
     * 设置out_order_no
     */
    public function setOutOrderNo($outOrderNo) {
        $this->outOrderNo = $outOrderNo;
    }
 
    /**
     * 设置subject
     */
    public function setSubject($subject) {
        $this->subject = $subject;
    }
 
    /**
     * 设置amount
     */
    public function setAmount($amount) {
        $this->amount = $amount;
    }
 
    /**
     * 设置notifyUrl
     */
    public function setNotifyUrl($notifyUrl) {
        $this->notifyUrl = $notifyUrl;
    }
 
 
    public function pay() {
        $params = [
            'out_trade_no' => $this->outOrderNo,
            // 'out_order_no' => $this->outOrderNo,
            'merchant_no' => $this->mercId,
            'total_amount' => $this->amount,
            // 'order_efficient_time' => date('YmdHis'),
        // 'notify_url' => $this->notifyUrl,
            // 'support_refund' => 1,
            // 'support_repeat_pay' => 1,
            // 'order_info' => $this->subject,
            // 'extend_info' => $this->subject,
            // 'counter_param' => json_encode(['pay_mode' => 'WECHAT']),

            'term_no' => $this->termno,
        // 'subject' => $this->subject,
        // 'detail' => '详情',
        // 'time_expired' => (string)(date('YmdHis') + 60*60*60),
            'mer_wx_mp_app_id' => $this->mer_wx_mp_app_id,
            'invoke_scene' => $this->invoke_scene,
            'rsk_inf' => [
                            // 'source_ip'=> $_SERVER['SERVER_ADDR']
                            'source_ip'=> '119.188.250.163'
                        ],
        ];
        
        $requestData = [
            'req_data' => $params,
            'version' => $this->version,
            // 'req_time' => (string)date('YmdHis'),
            'req_time' => date('YmdHis')
        ];


        $body = json_encode($requestData, JSON_UNESCAPED_UNICODE);
// echo '打印请求参';
// var_dump($body);
        $authorization = $this->getAuthorization($body);
// echo '打印请求头';
// var_dump($authorization);

        return $this->post($this->uoUrl, $body, $authorization);   



        // 2

//         $headers = [
//             "Authorization:" . $authorization,
//             "Accept: application/json",
//             "Content-Type:application/json",
//         ];
//         $response = Http::post($this->uoUrl,
//             // http_build_query($requestData),  // http_build_query()函数是将一个关联数组或对象转换为URL查询字符串。该函数可以自动地将数组或对象的键值对转换为符合URL规范的字符串，并用“&”符号连接它们。
//             $body,
//             [
//                 CURLOPT_HTTPHEADER => $headers
//             ]
//         );

//         $response = json_decode($response, true);

// var_dump($response);
    }
 
    //验签
    public function signatureVerification($authorization, $body) {
        $authorization = str_replace($this->schema . " ", "", $authorization);
        $authorization = str_replace(",","&", $authorization);
        $authorization = str_replace("\"","", $authorization);
        $authorization = $this->convertUrlQuery($authorization);
        
        $authorization['signature'] = base64_decode($authorization['signature']);
 
        $message = $authorization['timestamp'] . "\n" . $authorization['nonce_str'] . "\n" . $body . "\n";
 
        $key = openssl_get_publickey(file_get_contents($this->lklCertificatePath));
        $flag = openssl_verify($message, $authorization['signature'], $key, OPENSSL_ALGO_SHA256);
        openssl_free_key($key);
        if($flag) {
            return true;
        }
        return false;
    }
 
    //签名
    public function getAuthorization($body) {
        $nonceStr = $this->getRandom(12);
        // $nonceStr = "123456789012";
        $timestamp = time();
 
        $message = $this->appid . "\n" . $this->mchSerialNo . "\n" . $timestamp . "\n" . $nonceStr . "\n" . $body . "\n";
 
        $key = openssl_get_privatekey(file_get_contents($this->merchantPrivateKeyPath));
 
        openssl_sign($message, $signature, $key, OPENSSL_ALGO_SHA256);
        openssl_free_key($key);
 
        return $this->schema . " appid=\"" . $this->appid . "\"," . "serial_no=\"" . $this->mchSerialNo . "\"," . "timestamp=\"" . $timestamp . "\"," . "nonce_str=\"" . $nonceStr . "\"," . "signature=\"" . base64_encode($signature) . "\"";
    }
 
    //请求
    public function post($url, $data, $authorization) {
 
        $headers = [
            "Authorization: " . $authorization,
            "Accept: application/json",
            "Content-Type:application/json",
        ];
 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);//设置HTTP头
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        //         $error = curl_error($ch); 
        // var_dump($error);exit(0);

        curl_close($ch);
        return json_decode($res, true);
    }
 
    //签名参数转数组
    private function convertUrlQuery($query) { 
        $queryParts = explode('&', $query); 
         
        $params = array(); 
        foreach ($queryParts as $param) { 
            $item = explode('=', $param); 
            $params[$item[0]] = $item[1]; 
        }
        if($params['signature']) {
            $params['signature'] = substr($query, strrpos($query, 'signature=') + 10);
        }
         
        return $params; 
    }
 
    /**
     * 获取随机字符串
     */
    public function getRandom($len)
    {
        $base = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = strlen($base);
        $random = '';
        for ($i = 0; $i < $len; $i++) { 
            $random .= $base[rand(0, $count-1)];
        }
        return $random;
    }
    

     // 交易结果查询
    public  function tradeQuery()
    {
       $params = [
            'out_trade_no' => $this->outOrderNo,
            // 'trade_no' => $this->trade_no,
            'merchant_no' => $this->mercId,
            'term_no' => $this->termno,  
        ];
        
        $requestData = [
            'req_data' => $params,
            'version' => $this->version,
            'req_time' => date('YmdHis')
        ];

        $body = json_encode($requestData, JSON_UNESCAPED_UNICODE);
// echo '打印请求参';
// var_dump($body);
        $authorization = $this->getAuthorization($body);
// echo '打印请求头';
// var_dump($authorization);

        return $this->post($this->queryUrl, $body, $authorization);  
    }


    // 退货接口
    public  function tradeRefund()
    {
       $params = [
            'out_trade_no' => $this->outOrderNo,
            // 'trade_no' => $this->trade_no,
            'merchant_no' => $this->mercId,
            'term_no' => $this->termno,  
            'refund_amount' => $this->amount, 
            'notify_url' => $this->notifyUrl   // 退款回调地址
        ];
        
        $requestData = [
            'req_data' => $params,
            'version' => $this->version,
            'req_time' => date('YmdHis')
        ];

        $body = json_encode($requestData, JSON_UNESCAPED_UNICODE);
// echo '打印请求参';
// var_dump($body);
        $authorization = $this->getAuthorization($body);
// echo '打印请求头';
// var_dump($authorization);

        return $this->post($this->queryUrl, $body, $authorization);  


        // //if (!empty($response->code) && $response->code == 10000 && $response->fund_change == 'Y')
        // if (empty($response->code) || $response->code != 10000 || $response->fund_change != 'Y') {
        //     if(! empty($response->code)){
        //         throw new Exception('支付宝接口错误：'. ($response->sub_msg));
        //     }
        //     throw new Exception('支付宝接口错误');
        // }
    }


}

?>