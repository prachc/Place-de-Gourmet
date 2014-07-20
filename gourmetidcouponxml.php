<?php
//error_reporting(0);
ini_set('user_agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/534.3 (KHTML, like Gecko) Chrome/6.0.472.63 Safari/534.3');
include 'funct.php';

$id = $_GET['id'];
$mode = $_GET['mode'];

//http://r.gnavi.co.jp/e548700/map/

$map = "http://r.gnavi.co.jp/".$id."/map/";

//debug("map",$map);

//$url = substr($url,0,-1);

//$opts = array('http' => array('proxy' => 'tcp://dev-proxy.rakuten.co.jp:8311', 'request_fulluri' => true));
//$context = stream_context_create($opts);

$xml_string = file_get_contents($map);//,false,$context);

//echo $xml_string;

$dom = new DOMDocument();
$dom->loadHTML('<?xml encoding="UTF-8">'.$xml_string);
$xpath = new DOMXPath($dom);
$dom_lis = $xpath->evaluate("//dd[starts-with(@id, 'couponContent')]/ol/li");
$coupon = array();

$result ="<result>";
for ($i = 0; $i < $dom_lis->length; $i++) {
	$dom_li = $dom_lis->item($i);
	array_push($coupon, $dom_li->nodeValue);
	$result.="<coupon>";
	$result.=$coupon[$i];
	$result.="</coupon>";
}
$result.="</result>";
echo $result;
?>