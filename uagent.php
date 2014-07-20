<?php
$uagent = $_SERVER['HTTP_USER_AGENT'];

//$pos = strpos($uagent,"Android");

if (strpos($uagent, 'Android') !== false) {
	echo 'Android';
}else {
	echo 'Desktop';
}

//echo $uagent;
?>