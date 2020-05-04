<?php

$site_url 	= (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$base 		= 'http://api.xoli.ir/'; // 

$cookiedomain = str_replace("http://www.","",$base);
$cookiedomain = str_replace("https://www.","",$cookiedomain);
$cookiedomain = str_replace("www.","",$cookiedomain);

$url = $base . $_SERVER['REQUEST_URI'];

if($_SERVER['HTTPS'] == 'on'){
	$mydomain = 'https://'.$_SERVER['HTTP_HOST'];
} else {
	$mydomain = 'http://'.$_SERVER['HTTP_HOST'];
}

$curlSession = curl_init();

curl_setopt ($curlSession, CURLOPT_URL, $url);
curl_setopt ($curlSession, CURLOPT_HEADER, 1);


if($_SERVER['REQUEST_METHOD'] == 'POST'){
	curl_setopt ($curlSession, CURLOPT_POST, 1);
	curl_setopt ($curlSession, CURLOPT_POSTFIELDS, $_POST);
}

curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1);
curl_setopt($curlSession, CURLOPT_TIMEOUT,30);
curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, 0);

foreach($_COOKIE as $k=>$v){
	if(is_array($v)){
		$v = serialize($v);
	}
	curl_setopt($curlSession,CURLOPT_COOKIE,"$k=$v; domain=.$cookiedomain ; path=/");
}

$response = curl_exec ($curlSession);

if (curl_error($curlSession)){
        print curl_error($curlSession);
} else {

	$response = str_replace("HTTP/1.1 100 Continue\r\n\r\n","",$response);

	$ar = explode("\r\n\r\n", $response, 2); 


	$header = $ar[0];
	$body = $ar[1];

	$header_ar = split(chr(10),$header); 
	foreach($header_ar as $k=>$v){
		if(!preg_match("/^Transfer-Encoding/",$v)){
			$v = str_replace($base,$mydomain,$v); //header rewrite if needed
			header(trim($v));
		}
	}
    include 'api.php';
	$body = str_replace($base,$mydomain,$body);
	$body = str_replace('<div class="recent-posts">','<div class="rightad" style="margin-top: -28px;">'. $r_adv ,$body);
	$body = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $body);
	$body = preg_replace('#adsjava#is', 'your ads code here', $body); 
	$body = preg_replace('#mywebsiteaddres#is', 'yourwebsite.com', $body); 
	
	
	print $body;
}
curl_close ($curlSession);
?>
