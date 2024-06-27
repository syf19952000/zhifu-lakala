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
    $ch=curl_init(); //��ʼ��
    curl_setopt($ch,CURLOPT_URL,$curl);
    curl_setopt($ch,CURLOPT_HEADER,false);//���ò���Ҫͷ��Ϣ
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);//��ȡҳ�����ݣ��������
    if($https)
    {
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);//������������֤
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//�����ͻ�����֤
    }

    if($method=='post')
    {
        curl_setopt($ch, CURLOPT_POST,true);//����������post��ʽ
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//����post��������
        
    }

    $str=curl_exec($ch);//ִ�з���
    curl_close($ch);//�ر�curl���ͷ���Դ
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
    
    
    /*��ȡticket
expire_seconds:��ά����Ч�ڣ��룩
type����ά�����ͣ���ʱ�����ã�
scene:�������
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
/* ͨ��ticket��ȡ��ά�� */
public function _getQRCode($expire_seconds=604800,$type="temp",$scene=1)
{
$content=json_decode($this->_getTicket($expire_seconds,$type,$scene));
$ticket=$content->ticket;
$image=$this->_request("https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket));
//$file="./".$type.$scene.".jpg"; //����ͼƬ����
//file_put_contents($file,$content); //��ά�뱣�浽����
return $image;}}

?>