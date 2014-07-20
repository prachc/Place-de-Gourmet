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
 	'hit_per_page'  => '20', 
    'range' => '4',
);

foreach ($params1 as $key => $value) {
    $query_string .= "$key=" . $value . "&";
}

$url = "$base?$query_string";
$url = substr($url,0,-1);


//$opts = array('http' => array('proxy' => 'tcp://dev-proxy.rakuten.co.jp:8311', 'request_fulluri' => true));
//$context = stream_context_create($opts);

//debug("URL",$url);

$xml_string = file_get_contents($url);//,false,$context);
$xml=simplexml_load_string($xml_string);

//debug("XML",$xml_string);
$id=$xml->xpath("//id");
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
	$coupon = connectGourmetIdCoupon($id[$i], $context);
	//if($hotelName[$i]!=''&&$nearestStation[$i]!=''&&$latitude[$i]!=''&&$longitude[$i]!=''){
		$result.=("{");
		$result.=("\""."id"."\":\"".$id[$i]."\",");
		$result.=("\""."name"."\":\"".filterRestaurant($name[$i])."\",");
		$result.=("\""."category"."\":\"".$category[$i]."\",");
		$result.=("\""."coupon"."\":".$coupon.",");
		$result.=("\""."pr"."\":\"".$pr[$i]."\",");
		$result.=("\""."latitude"."\":\"".$latitude[$i]."\",");
		$result.=("\""."longitude"."\":\"".$longitude[$i]."\",");
		$result.=("\""."restaurantImageUrl"."\":\"".$shop_image1[$i]."\",");
		$result.=("\""."restaurantUrl"."\":\"".$restaurantUrl[$i]."\",");
		$result.=("\""."budget"."\":\"".$budget[$i]."\",");
		$result.=("\""."address"."\":\"".filterGAddr($address[$i])."\"");
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

function connectGourmetIdCoupon($gid,$ccontext){
	$curl = 'http://127.0.0.1/placedegourmet-php/gourmetidcouponxml.php?id='.$gid;
	$cxml_string = file_get_contents($curl);
	$cxml=simplexml_load_string($cxml_string);
	//debug("cxml_string",$cxml_string);
	$ccoupon=$cxml->xpath("//coupon");
	
	$result="[";
	
	for ($i = 0; $i < sizeof($ccoupon); $i++) {
		$result.=("\"".trim($ccoupon[$i])."\",");
	}
	
	if(strlen($result)!=1)
		$result = substr($result,0,-1);
	
	$result.="]";
	//debug("result",$result);
	return $result;
}

?>