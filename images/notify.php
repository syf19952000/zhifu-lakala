<?php
header('Content-Type: text/plain; charset=utf-8');
require_once("inc/db.class.php");




$simple = json_decode(json_encode(simplexml_load_string($GLOBALS['HTTP_RAW_POST_DATA'], 'SimpleXMLElement', LIBXML_NOCDATA)), true);



	$sql = "INSERT INTO `order` (`biao`) VALUES('ddddddd222')";
	$connect->query($sql);
	
/**
$xml = file_get_contents('php:input');
$data = xmlToArray($xml);
$text = $data['result_code'];
if($data['return_code']=='SUCCESS'){
	$sql = "INSERT INTO `order` (`biao`) VALUES('cccccc')";
	$connect->query($sql);
}


private function xmlToArray($xml)
{
	libxml_disable_entiy_loader(true);
	$values = json_decode(json_decode(simplexml_load_string($xml,'simpleXMLElement',LIBXML_NOCDATA)),true);
	return $values;
}






	$sql = "INSERT INTO `order` (`biao`) VALUES('aa')";
	$connect->query($sql);
	
**/

