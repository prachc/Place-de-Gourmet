<?php

$url = 'http://map.simpleapi.net/stationapi';
$url .= '?x=' . $_REQUEST["lng"];
$url .= '&y=' . $_REQUEST["lat"];
$url .= '&output=xml';

//$opts = array('http' => array('proxy' => 'tcp://dev-proxy.rakuten.co.jp:8311', 'request_fulluri' => true));
//$context = stream_context_create($opts);
$xml_string = file_get_contents($url);//,false,$context);
$xml=simplexml_load_string($xml_string);
//echo $data;

$name=$xml->xpath("//name");
//$clatitude=$xml->xpath("//latitude");
//$clongitude=$xml->xpath("//longitude");

echo "{\"nearestStation\":\"".substr($name[0],0,strlen($name[0])-3)."\"}";
//return $result;

//$buf = unserialize($data);

/*foreach($buf as $one){
 $arr[] = array(
  "name"=>$one["line"] . $one["name"],
  "city"=>$one["city"]
 );
}
$json = new Services_JSON;
$encode = $json->encode($arr);
header("Content-Type: text/javascript; charset=utf-8");
echo $encode;*/
?>