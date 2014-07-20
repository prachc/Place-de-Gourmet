<?php
error_reporting(0);
ini_set('user_agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/534.3 (KHTML, like Gecko) Chrome/6.0.472.63 Safari/534.3');
include 'funct.php';

$station = $_GET['station'];
$mode = $_GET['mode'];
$morepage = false;

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

$base = 'http://dining.rakuten.co.jp/result/list/page/1/keyword/'.$station.'/';
$url = $base;
//$url = 'http://dining.rakuten.co.jp/result/list?keyword='.$station;
//echo $url;
//$url = substr($url,0,-1);

//$opts = array('http' => array('proxy' => 'tcp://dev-proxy.rakuten.co.jp:8311', 'request_fulluri' => true));
//$context = stream_context_create($opts);
//debug"URL",$url);

$xml_string = file_get_contents($url);//,false,$context);
$dom = new DOMDocument();
$dom->loadHTML('<?xml encoding="UTF-8">'.$xml_string);

$name=array();
$restaurantUrl = array();	
$category=array();	
$budget = array();
$pr = array();
$restaurantImageUrl= array();
$storeNumber= array();
$haspoint;
$hasreserve;

$xpath = new DOMXPath($dom);
$pages = $xpath->evaluate("//ul[starts-with(@class, 'searchTextNumber')]/li/a");

if($pages->length!=0) 
	$morepage = true;
else $morepage = false;

//debug("morepage",$morepage);


/*$latitude=$xml->xpath("//latitude");
$longitude=$xml->xpath("//longitude");
$address=$xml->xpath("//address");*/


/*for ($i = 0; $i < $pages->length; $i++) {
	$href = $pages->item($i);
	$link = $href->getAttribute('href');
	echo $link."<br>";
}*/


$dom_restaurantUrls = $xpath->evaluate("//dd[starts-with(@class, 'detailName')]/a");

for ($i = 0; $i < $dom_restaurantUrls->length; $i++) {
	$dom_restaurantUrl = $dom_restaurantUrls->item($i);
	array_push($name, $dom_restaurantUrl->nodeValue);
	//debug("name[".$i."]",$name[$i]);
}

for ($i = 0; $i < $dom_restaurantUrls->length; $i++) {
	$dom_restaurantUrl = $dom_restaurantUrls->item($i);
	$tmparray = explode("/",$dom_restaurantUrl->getAttribute('href'));
	array_push($restaurantUrl,$dom_restaurantUrl->getAttribute('href'));
	array_push($storeNumber,$tmparray[4]);
	//debug("restaurantUrl[".$i."]",$restaurantUrl[$i]);
	//debug("storeNumber[".$i."]",$storeNumber[$i]);
}

$dom_categories = $xpath->evaluate("//dl[starts-with(@class,'searchMainText')]/dt");

for ($i = 0; $i < $dom_categories->length; $i++) {
	$dom_category = $dom_categories->item($i);
	//$gtext = $genre->getAttribute('href');
	array_push($category,filterCategory($dom_category->nodeValue));
	//debug("category[".$i."]",$category[$i]);
}

$dom_budgets = $xpath->evaluate("//div[starts-with(@class,'searchSubText')]/dl/dd[1]");

for ($i = 0; $i < $dom_budgets->length; $i++) {
	$dom_budget = $dom_budgets->item($i);
	array_push($budget,filterBudget($dom_budget->nodeValue));
	//debug("budget[".$i."]",$budget[$i]);
}



$dom_prs = $xpath->evaluate("//dd[starts-with(@class, 'description')]");

for ($i = 0; $i < $dom_prs->length; $i++) {
	$dom_pr = $dom_prs->item($i);
	//$gtext = $genre->getAttribute('href');
	array_push($pr,$dom_pr->nodeValue);
	//debug("pr[".$i."]",$pr[$i]);
}

$dom_images = $xpath->evaluate("//p[starts-with(@class, 'searchPhoto')]/a/img");

for ($i = 0; $i < $dom_images->length; $i++) {
	$dom_image = $dom_images->item($i);
	//$gtext = $genre->getAttribute('href');
	array_push($restaurantImageUrl,"http:".$dom_image->getAttribute('src'));
	//debug("restaurantImageUrl[".$i."]",$restaurantImageUrl[$i]);
}

$dom_points = $xpath->evaluate("//a[starts-with(@class, 'pointIcon')]");
//debug("dompoints",$dom_points->length);

if($dom_points->length==0)
	$haspoint=false;
else
	$haspoint=true;

$dom_reserves = $xpath->evaluate("//a[starts-with(@class, 'webPointIcon')]");
//debug("domreserves",$dom_reserves->length);

if($dom_reserves->length==0)
	$hasreserve=false;
else
	$hasreserve=true;

/*if($morepage){
	$base = 'http://dining.rakuten.co.jp/result/list/page/2/keyword/'.$station.'/';
	$url = $base;
	$xml_string = file_get_contents($url,false,$context);
	$dom = new DOMDocument();
	$dom->loadHTML('<?xml encoding="UTF-8">'.$xml_string);
	
	$xpath = new DOMXPath($dom);
	
	$dom_restaurantUrls = $xpath->evaluate("//dt/a[starts-with(@href, '/store/')]");
	
	for ($i = 0; $i < $dom_restaurantUrls->length; $i++) {
		$dom_restaurantUrl = $dom_restaurantUrls->item($i);
		array_push($name, $dom_restaurantUrl->nodeValue);
		//debug("name[".$i."]",$name[$i]);
	}
	
	for ($i = 0; $i < $dom_restaurantUrls->length; $i++) {
		$dom_restaurantUrl = $dom_restaurantUrls->item($i);
		$tmparray = explode("/",$dom_restaurantUrl->getAttribute('href'),3);
		array_push($restaurantUrl,"http://dining.rakuten.co.jp".$dom_restaurantUrl->getAttribute('href'));
		array_push($storeNumber,$tmparray[2]);
		//debug("restaurantUrl[".$i."]",$restaurantUrl[$i]);
		//debug("storeNumber[".$i."]",$storeNumber[$i]);
	}
	
	$dom_categories = $xpath->evaluate("//p[starts-with(@class, 'genre')]");
	
	for ($i = 0; $i < $dom_categories->length; $i++) {
		$dom_category = $dom_categories->item($i);
		//$gtext = $genre->getAttribute('href');
		array_push($category,$dom_category->nodeValue);
		//debug("category[".$i."]",$category[$i]);
	}
	
	$dom_budgets = $xpath->evaluate("//div[starts-with(@class, 'moreInfo')]/dl/dd");
	
	for ($i = 0; $i < $dom_budgets->length; $i++) {
		$dom_budget = $dom_budgets->item($i);
		//$gtext = $genre->getAttribute('href');
		array_push($budget,filterBudget($dom_budget->nodeValue));
		//debug("budget[".$i."]",$budget[$i]);
	}
	
	$dom_prs = $xpath->evaluate("//div[starts-with(@class, 'mainInfo')]/dl/dd/em");
	
	for ($i = 0; $i < $dom_prs->length; $i++) {
		$dom_pr = $dom_prs->item($i);
		//$gtext = $genre->getAttribute('href');
		array_push($pr,$dom_pr->nodeValue);
		//debug("pr[".$i."]",$pr[$i]);
	}
	
	$dom_images = $xpath->evaluate("//p[starts-with(@class, 'shopImg')]/a/img");
	
	for ($i = 0; $i < $dom_images->length; $i++) {
		$dom_image = $dom_images->item($i);
		//$gtext = $genre->getAttribute('href');
		array_push($restaurantImageUrl,$dom_image->getAttribute('src'));
		//debug("restaurantImageUrl[".$i."]",$restaurantImageUrl[$i]);
	}
}*/

////printing JSON
$result = "";
$result.="[";
for($i = 0, $size = sizeof($name); $i < $size; ++$i){
	//$geo = connectStoreNumberGeo($storeNumber[$i],$context);
	$geo = connectStoreNumberGeo($storeNumber[$i]);
	if($geo['address']!=""){
		$result.=("{");
		$result.=("\""."name"."\":\"".filterRestaurant($name[$i])."\",");
		$result.=("\""."category"."\":\"".$category[$i]."\",");
		$result.=("\""."pr"."\":\"".$pr[$i]."\",");
		$result.=("\""."latitude"."\":\"".$geo['latitude']."\",");
		$result.=("\""."longitude"."\":\"".$geo['longitude']."\",");
		$result.=("\""."restaurantImageUrl"."\":\"".$restaurantImageUrl[$i]."\",");
		$result.=("\""."restaurantUrl"."\":\"".$restaurantUrl[$i]."\",");
		$result.=("\""."budget"."\":\"".$budget[$i]."\",");  //end with ,
		$result.=("\""."point"."\":\"".genPoint($haspoint)."\",");
		$result.=("\""."reserve"."\":\"".genReserve($hasreserve)."\",");
		$result.=("\""."address"."\":\"".$geo['address']."\"");
		$result.=("}");
		$result.=(",");
	}
	//debug("address",$geo['address']);
}
$result = substr($result,0,-1);
$result.="]";

echo $result;
//$xml=simplexml_load_string($xml_string);

////a[@class='bf' and starts-with(@href, '/book/')]
//$links = $xml->xpath("//a");
//echo $xml;
//echo $links.length;
//echo $url;
//foreach ($links as $link)
	//echo $link."<br>";
function connectStoreNumberGeo($cstorenumber){
	$curl = 'http://127.0.0.1/placedegourmet-php/storenumbergeoxml.php?storenumber='.$cstorenumber;
	//debug("curl",$curl);
	$cxml_string = file_get_contents($curl);
	//debug("cxml", $cxml_string);
	$cxml=simplexml_load_string($cxml_string);
	
	$cddress=$cxml->xpath("//address");
	$clatitude=$cxml->xpath("//latitude");
	$clongitude=$cxml->xpath("//longitude");
	
	$result = array(
		'address' => $cddress[0],
		'latitude' => $clatitude[0],
		'longitude' => $clongitude[0],
	);
	
	return $result;
}

function genPoint($gpoint){
	if($gpoint==false)
		return "";
	else
		return "【楽天ポイントが貯まる】";
}

function genReserve($greserve){
	if($greserve==false)
		return "";
	else
		return "【ＷＥＢ予約で１００ポイント】";
}
?>