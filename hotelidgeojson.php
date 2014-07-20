<?
//error_reporting(0);
ini_set('user_agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/534.3 (KHTML, like Gecko) Chrome/6.0.472.63 Safari/534.3');
include 'funct.php';

$id = $_GET['id'];
$mode = $_GET['mode'];

$base = 'http://api.rakuten.co.jp/rws/3.0/rest';
$query_string = "";
$params1 = array( 'developerId' => 'ca09e6d3a0c98fc5ac47598c0b9c2d02',
    'operation'  => 'SimpleHotelSearch',
    'version'  => '2009-10-20',
	'datumType' => '1',
	'hotelNo' => $id,
);

foreach ($params1 as $key => $value) {
    $query_string .= "$key=" . $value . "&";
}

$url = "$base?$query_string";
$url = substr($url,0,-1);

//$opts = array('http' => array('proxy' => 'tcp://dev-proxy.rakuten.co.jp:8311', 'request_fulluri' => true));
//$context = stream_context_create($opts);

$xml_string = file_get_contents($url);//,false,$context);
$xml=simplexml_load_string($xml_string);

$hotelName=$xml->xpath("//hotelName");
$hotelNo=$xml->xpath("//hotelNo");
$nearestStation=$xml->xpath("//nearestStation");
$latitude=$xml->xpath("//latitude");
$longitude=$xml->xpath("//longitude");
$hotelImageUrl=$xml->xpath("//hotelImageUrl");
$hotelThumbnailUrl=$xml->xpath("//hotelThumbnailUrl");
$postalCode=$xml->xpath("//postalCode");
$hotelInformationUrl=$xml->xpath("//hotelInformationUrl");
$hotelMinCharge=$xml->xpath("//hotelMinCharge");
$address1=$xml->xpath("//address1");
$address2=$xml->xpath("//address2");

$result = "";
$result.="[";
for($i = 0, $size = sizeof($hotelName); $i < $size; ++$i){
	if($hotelName[$i]!=''&&$nearestStation[$i]!=''&&$latitude[$i]!=''&&$longitude[$i]!=''){
		$result.=("{");
		$result.=("\""."hotelName"."\":\"".$hotelName[$i]."\",");
		$result.=("\""."hotelNo"."\":\"".$hotelNo[$i]."\",");
		$result.=("\""."nearestStation"."\":\"".filterStation($nearestStation[$i])."\",");
		$result.=("\""."latitude"."\":\"".$latitude[$i]."\",");
		$result.=("\""."longitude"."\":\"".$longitude[$i]."\",");
		$result.=("\""."hotelImageUrl"."\":\"".$hotelImageUrl[$i]."\",");
		$result.=("\""."hotelThumbnailUrl"."\":\"".$hotelThumbnailUrl[$i]."\",");
		$result.=("\""."hotelInformationUrl"."\":\"".$hotelInformationUrl[$i]."\",");
		$result.=("\""."hotelMinCharge"."\":\"".$hotelMinCharge[$i]."\",");
		$result.=("\""."postalCode"."\":\"".$postalCode[$i]."\",");
		$result.=("\""."address"."\":\"".$address1[$i].filterAddr($address2[$i])."\"");
		$result.=("}");
		$result.=(",");
	}
}
$result = substr($result,0,-1);
$result.="]";
echo $result;
//debug("XML",$url);

?>