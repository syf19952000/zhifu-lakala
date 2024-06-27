<?php
class WeChat{
    private $_appid;
    private $_appsecret;
    private $_token;
    
    public function __construct($appid,$appsecret,$token)
    {
        $this->_appid=$appid;
        $this->_appsecret=$appsecret;
        $this->_token=$token;
    }
    private function _request($curl,$https=true,$method='get',$data=null)
    {
    $ch=curl_init(); //初始化
    curl_setopt($ch,CURLOPT_URL,$curl);
    curl_setopt($ch,CURLOPT_HEADER,false);//设置不需要头信息
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);//获取页面内容，但不输出
    if($https)
    {
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);//不做服务器认证
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//不做客户端认证
    }

    if($method=='post')
    {
        curl_setopt($ch, CURLOPT_POST,true);//设置请求是post方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//设置post请求数据
        
    }

    $str=curl_exec($ch);//执行访问
    curl_close($ch);//关闭curl，释放资源
    return $str;    
    }


    private function _getAccesstoken()
    {
        $file="./accesstoken";
        if(file_exists($file))
        {
            $content=file_get_contents($file);
            $content=json_decode($content);
            if(time()-filemtime($file)<$content->expires_in)
            {
                return $content->access_token;
            }
        }
        $content=_request("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->_appid."&secret=".$this->_appsecret);
        file_put_contents($file,$content);
        $content=json_decode($content);
        return $content->access_token;    
    }
    
    
    /*获取ticket
expire_seconds:二维码有效期（秒）
type：二维码类型（临时或永久）
scene:场景编号
*/
    public function _getTicket($expire_seconds=604800,$type="temp",$scene=1)
    {        
        if($type=="temp"){
        $data='{"expire_seconds": '.$expire_seconds.', "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$scene.'}}}';
		var_dump($this->_request("https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->_getAccesstoken(),true,"post",$data));exit;
        return $this->_request("https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->_getAccesstoken(),true,"post",$data);
        }else
        {
        $data='{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$scene.'}}}';
		var_dump($this->_request("https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->_getAccesstoken(),true,"post",$data));exit;
        return $this->_request("https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->_getAccesstoken(),true,"post",$data);    
        }    
    }
/* 通过ticket获取二维码 */
public function _getQRCode($expire_seconds=604800,$type="temp",$scene=1)
{
$content=json_decode($this->_getTicket($expire_seconds,$type,$scene));
$ticket=$content->ticket;
$image=$this->_request("https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket));
//$file="./".$type.$scene.".jpg"; //设置图片名字
//file_put_contents($file,$content); //二维码保存到本地
return $image;}}

?>