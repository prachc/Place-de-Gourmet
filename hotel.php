<?
$base = 'http://api.rakuten.co.jp/rws/3.0/rest';
$query_string = "";

$params1 = array( 'developerId' => 'ca09e6d3a0c98fc5ac47598c0b9c2d02',
    'operation'  => 'SimpleHotelSearch',
    'version'  => '2009-09-09',
	'datumType' => '1',
    'latitude'  => '35.6065914',
    'longitude'  => '139.7513225',
    'searchRadius'     => '1',
);

$params2 = array( 'developerId' => 'ca09e6d3a0c98fc5ac47598c0b9c2d02',
    'operation'  => 'SimpleHotelSearch',
    'version'  => '2009-09-09',
	'datumType' => '2',
    'latitude'  => '128440.51',
    'longitude'  => '503172.21',
    'searchRadius'     => '1',
);

/*
http://api.rakuten.co.jp/rws/3.0/rest?
developerId=ca09e6d3a0c98fc5ac47598c0b9c2d02
&operation=ItemSearch
&version=2010-09-15
&keyword=%E7%A6%8F%E8%A2%8B
&sort=%2BitemPrice
*/


foreach ($params1 as $key => $value) {
    $query_string .= "$key=" . $value . "&";
}

$url = "$base?$query_string";
$url = substr($url,0,-1);

//$opts = array('http' => array('proxy' => 'tcp://dev-proxy.rakuten.co.jp:8301', 'request_fulluri' => true));
//$context = stream_context_create($opts);



//$s = file_get_contents('http://www.google.com', false, $context);

$xml_string = file_get_contents($url);//,false,$context);

$xml=simplexml_load_string($xml_string);

echo "URL:".$url;
echo "<br>";
echo "<br>";

$hotelName=$xml->xpath("//hotelName");
$nearestStation=$xml->xpath("//nearestStation");
$latitude=$xml->xpath("//latitude");
$longitude=$xml->xpath("//longitude");
$hotelImageUrl=$xml->xpath("//hotelImageUrl");
$hotelThumbnailUrl=$xml->xpath("//hotelThumbnailUrl");
$postalCode=$xml->xpath("//postalCode");
$address1=$xml->xpath("//address1");
$address2=$xml->xpath("//address2");

for($i = 0, $size = sizeof($hotelName); $i < $size; ++$i){
	if($hotelName[$i]!=''&&$nearestStation[$i]!=''&&$latitude[$i]!=''&&$longitude[$i]!=''){
		echo "hotelName:".$hotelName[$i].'<br>';
		echo "nearestStation:".filterStation($nearestStation[$i]).'<br>';
		echo "latitude:".$latitude[$i].'<br>';
		echo "longitude:".$longitude[$i].'<br>';
		echo "hotelImageUrl:".$hotelImageUrl[$i].'<br>';
		echo "hotelThumbnailUrl:".imgTag($hotelThumbnailUrl[$i]).'<br>';
		echo "postalCode:".($postalCode[$i]).'<br>';
		//echo "address1:".($address1[$i]).'<br>';
		//echo "address2:".filterAddr($address2[$i]).'<br>';
		echo "address:".$address1[$i].filterAddr($address2[$i]).'<br>';
		$result = connectGourNavi($latitude[$i], $longitude[$i]);
		$names = $result["name"];
		$lats = $result["latitude"];
		$lngs = $result["longitude"];
		for ($j = 0, $sizej = sizeof($names); $j < $sizej; ++$j){
			echo "RName:".filterRestaurant($names[$j])."<br>";
			echo "RLat:".filterRestaurant($lats[$j])."<br>";
			echo "RLng:".filterRestaurant($lngs[$j])."<br>";
		}
		/*foreach ($names as $name){
			echo "name:".$name."<br>";
		}*/
		echo '<br>';
		
		
	}else
		;
}

/*foreach($stations as $station) { 
    echo $station.'<br>'; 
} */
echo "<br>";
echo $xml->asXML();

function imgTag($link){
	return "<img src='".$link."'/>";
}

function filterAddr($addr){
	$array1 = explode("　",$addr,2);
	$array2 = explode(" ",$array1[0],2);
	$array3 = explode("（",$array2[0],2);
	
	preg_match("/[\x{4e00}-\x{9fa5}]+.*\-[0-9]+/u", $array3[0], $matches);
	
	return $matches[0];
}

function filterStation($station){
	$array1 = explode("　",$station,2);
	$array2 = explode(" ",$array1[0],2);
	$array3 = explode("（",$array2[0],2);
	
	return $array3[0];
}

function filterRestaurant($restaurant){
	return str_replace( '<br>', '', $restaurant );
}

function connectGourNavi($latitude,$longitude){
	//echo "coor:".$latitude.",".$longitude;
	
	$base_gnavi = 'http://api.gnavi.co.jp/ver1/RestSearchAPI/';
	$query_gnavi = "";
	
	$params_gnavi = array( 'keyid' => '10d9098dba2f680c748de5b03b28940d',
		'input_coordinates_mode' => '2',
	    'latitude'  => $latitude,
	    'longitude'  => $longitude,
	    'range'     => '3',
	);

	foreach ($params_gnavi as $key => $value) {
	    $query_gnavi .= "$key=" . $value . "&";
	}
	
	$url_gnavi = "$base_gnavi?$query_gnavi";

	echo "url=".$url_gnavi."<br>";

	//$opts_gnavi = array('http' => array('proxy' => 'tcp://dev-proxy.rakuten.co.jp:8301', 'request_fulluri' => true));
	//$context_gnavi = stream_context_create($opts_gnavi);
	
	//$s = file_get_contents('http://www.google.com', false, $context);
	$xml_string_gnavi = file_get_contents($url_gnavi);//,false,$context_gnavi);
	
	//echo "xml:".$xml_string_gnavi;
	
	$xml_gnavi=simplexml_load_string($xml_string_gnavi);
	
	$name=$xml_gnavi->xpath("//name");
	$latitude=$xml_gnavi->xpath("//latitude");
	$longitude=$xml_gnavi->xpath("//longitude");
	
	$result = array(
		'name' => $name,
		'latitude' => $latitude,
		'longitude' => $longitude,
	);
	
	return $result;
}
?>