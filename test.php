<?php
include 'funct.php';
include 'stringbuffer.php';
$lat = $_GET['lat'];
$lng = $_GET['lng'];
//$sbuffer = new String_Buffer();
//debug("coor","lat:".$lat.",lng:".$lng);
$base = 'http://api.gnavi.co.jp/ver1/RestSearchAPI/';
$query_string = "";

$params1 = array( 'keyid' => '10d9098dba2f680c748de5b03b28940d',
    'input_coordinates_mode'  => '2',
	'coordinates_mode'  => '2',
	'latitude'  => $lat,
	'longitude'  => $lng,
 	'hit_per_page'  => '10', 
    'range' => '4',
);

foreach ($params1 as $key => $value) {
    $query_string .= "$key=" . $value . "&";
}

$url = "$base?$query_string";
$url = substr($url,0,-1);


$opts = array('http' => array('proxy' => 'tcp://dev-proxy.rakuten.co.jp:8311', 'request_fulluri' => true));
$context = stream_context_create($opts);

//debug("URL",$url);

$xml_string = file_get_contents($url,false,$context);
$xml=simplexml_load_string($xml_string);

//debug("XML",$xml_string);

$name=$xml->xpath("//name");
$category=$xml->xpath("//category");
$pr = $xml->xpath("//pr_short");
$latitude=$xml->xpath("//latitude");
$longitude=$xml->xpath("//longitude");
$shop_image1=$xml->xpath("//shop_image1");
$restaurantUrl = $xml->xpath("//url");
$budget = $xml->xpath("//budget");
$address=$xml->xpath("//address");

$result = "";
$result.="[";
for($i = 0, $size = sizeof($name); $i < $size; ++$i){
	//if($hotelName[$i]!=''&&$nearestStation[$i]!=''&&$latitude[$i]!=''&&$longitude[$i]!=''){
		$result.=("{");
		$result.=("\""."name"."\":\"".json_encode(filterRestaurant($name[$i]))."\",");
		$result.=("\""."category"."\":\"".json_encode($category[$i])."\",");
		$result.=("\""."pr"."\":\"".json_encode($pr[$i])."\",");
		$result.=("\""."latitude"."\":\"".json_encode($latitude[$i])."\",");
		$result.=("\""."longitude"."\":\"".json_encode($longitude[$i])."\",");
		$result.=("\""."restaurantImageUrl"."\":\"".json_encode($shop_image1[$i])."\",");
		$result.=("\""."restaurantUrl"."\":\"".json_encode($restaurantUrl[$i])."\",");
		$result.=("\""."budget"."\":\"".json_encode($budget[$i])."\",");
		$result.=("\""."address"."\":\"".json_encode(filterGAddr($address[$i]))."\"");
		$result.=("}");
		$result.=(",");
	//}
}
$result = substr($result,0,-1);
$result.="]";



//'[ {\"key\":\"aaa\"}, {\"key\":\"bbb\"} ]'
//debug("hotelName",$sbuffer->__toString());

//for($i = 0, $size = sizeof($hotelName); $i < $size; ++$i){
	
//}



/*function append($str){
	
}*/

echo str_replace('<br>', '', $result );

?>