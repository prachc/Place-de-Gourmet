<?php
function debug($name,$msg){
	echo "<b><font color='red'>".$name."</font></b> ".$msg."<br>\n";
}
function imgTag($link){
	return "<img src='".$link."'/>";
}

function filterAddr($addr){
	$array1 = explode("　",$addr,2);
	$array2 = explode(" ",$array1[0],2);
	$array3 = explode("（",$array2[0],2);
	
	preg_match("/[\x{4e00}-\x{9fa5}]+.*\-[0-9]+/u", $array3[0], $matches);
	
	return trim($matches[0]);
}

function filterBudget($budget){
	$temparray = explode(",", $budget);
	
	$final;
	//preg_match("/[0-9]+,[0-9]+.*[\x{4e00}-\x{9fa5}]+/u", $budget, $matches);
	//echo $temparray->length;
	//return $matches[0];
	if(sizeof($temparray)>1)
		$final = $temparray[0].substr($temparray[1], 0,3);
	else 
		$final = substr($budget,0,3)."00";
	
	$final = trim($final);
		
	if(is_numeric($final))
		return $final;
	else return "N/A";
	//return $final;
}

function filterGAddr($addr){
	$array1 = explode(" ",$addr);
	
	return $array1[1];
}


function filterStation($station){
	$array1 = explode("　",$station,2);
	$array2 = explode(" ",$array1[0],2);
	$array3 = explode("（",$array2[0],2);
	
	return $array3[0];
}



function filterRestaurant($restaurant){
	return str_replace( '<br>', '', trim($restaurant) );
}
function utf8json($inArray) { 

    static $depth = 0; 

    /* our return object */ 
    $newArray = array(); 

    /* safety recursion limit */ 
    $depth ++; 
    if($depth >= '30') { 
        return false; 
    } 

    /* step through inArray */ 
    foreach($inArray as $key=>$val) { 
        if(is_array($val)) { 
            /* recurse on array elements */ 
            $newArray[$key] = utf8json($val); 
        } else { 
            /* encode string values */ 
            $newArray[$key] = utf8_encode($val); 
        } 
    } 

    /* return utf8 encoded array */ 
    return $newArray; 
}
function filterCategory($cat){
	$array = explode(" ", $cat);
	return $array[0];
}
?>