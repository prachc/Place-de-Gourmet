<?php
error_reporting(0);
//ini_set('user_agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/534.3 (KHTML, like Gecko) Chrome/6.0.472.63 Safari/534.3');
include 'funct.php';

$storenumber = $_GET['storenumber'];
$mode = $_GET['mode'];

//http://dining.rakuten.co.jp/result/list/page/1/keyword/%E8%87%AA%E7%94%B1%E3%81%8C%E4%B8%98%E9%A7%85/

/*$base = 'http://dining.rakuten.co.jp/result/list';
$query_string = "";
//http://dining.rakuten.co.jp/result/list?keyword=%E8%87%AA%E7%94%B1%E3%81%8C%E4%B8%98&di_parent_genre_id=

$params1 = array( 'keyword' => $station,
    'di_parent_genre_id'  => '',
);

foreach ($params1 as $key => $value) {
    $query_string .= "$key=" . $value . "&";
}*/
//$url = "$base?$query_string";

$map = "http://dining.rakuten.co.jp/store/".$storenumber."/";
//debug("map",$map);
      //http://dining.rakuten.co.jp/store/map/1000069833
//$url = $base;


//$url = substr($url,0,-1);

//$opts = array('http' => array('proxy' => 'tcp://dev-proxy.rakuten.co.jp:8311', 'request_fulluri' => true));
//$context = stream_context_create($opts);

//debug("URL",$map);



$xml_string = file_get_contents($map);//,false,$context);

//echo $xml_string;

$dom = new DOMDocument();
$dom->loadHTML('<?xml encoding="UTF-8">'.$xml_string);
$xpath = new DOMXPath($dom);
$dom_tds = $xpath->evaluate("//div[starts-with(@id, 'storeInfoBox')]/table/tr/td");
$tds = array();

for ($i = 0; $i < $dom_tds->length; $i++) {
	$dom_td = $dom_tds->item($i);
	array_push($tds, $dom_td->nodeValue);
	////debug("addresses[".$i."]",$addresses[$i]);
}

$address = filterAddr($tds[0]);

//debug("address",$address);

$geo = 'http://map.yahooapis.jp/LocalSearchService/V1/LocalSearch?appid=MpqmctGxg67f7eYOVOFhs3.l1vXPMmJKVewWOMqftzc9mOPCk_624AQ8yGBgfmsM.w--&p='.$address;
//MpqmctGxg67f7eYOVOFhs3.l1vXPMmJKVewWOMqftzc9mOPCk_624AQ8yGBgfmsM.w--
//debug("geo",$geo);

$xml_string = file_get_contents($geo,false,$context);
$xml=simplexml_load_string($xml_string);

$tmp = $xml->xpath("//DatumWgs84/Lat");
$lat= $tmp[0];

$tmp=$xml->xpath("//DatumWgs84/Lon");
$lng= $tmp[0];

//debug("lat",$lat);
//debug("lng",$lng);

$result ="<result>";
	$result.="<address>";
	$result.=$address;
	$result.="</address>";
	$result.="<latitude>";
	$result.=$lat;
	$result.="</latitude>";
	$result.="<longitude>";
	$result.=$lng;
	$result.="</longitude>";
$result.="</result>";

echo $result;

//$xml=simplexml_load_string($xml_string);

////a[@class='bf' and starts-with(@href, '/book/')]
//$links = $xml->xpath("//a");
//echo $xml;
//echo $links.length;
//echo $url;
//foreach ($links as $link)
	//echo $link."<br>";
?>