<?php
include_once("check_login_status.php");


// $ch = curl_init('http://codepen.io/bennettfeely/pen/ErFGv');
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 	curl_setopt ($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
// 	$cl = curl_exec($ch);

// 	$dom = new DOMDocument();
// 	@$dom->loadHTML($cl);

// 	$title = $dom->getElementsByTagName("meta");
// 	// echo "<pre>";
// 	// print_r($title);
	
// 	// $t = '';

// 	foreach($title as $link){
// 		// echo '=>'.$link->getAttribute('property');
// 		if($link->getAttribute('property') == 'og:image'){
// 			echo '<img src="'.$link->getAttribute('content').'" >';
// 		}
// 	}


	// echo $t;


$site = 'https://dribbble.com/shots/1371032-todo-app-notification?list=searches&tag=todo_app&offset=236';
$siteExplode = explode("/",$site);

$link = $siteExplode[0].$siteExplode[1].$siteExplode[2];

$finalLinkForIco = $siteExplode[0].'//'.$siteExplode[1].$siteExplode[2];

$favicon_url = $finalLinkForIco."/favicon.ico";
$ico = "";
$headers = get_headers($favicon_url);
if(preg_match("|200|", $headers[0])){
	$ico = $favicon_url;
}else{
	$ch = curl_init($site);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt ($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
	$cl = curl_exec($ch);

	$dom = new DOMDocument();
	@$dom->loadHTML($cl);

	$title = $dom->getElementsByTagName("link");

	foreach($title as $link){
		if($link->getAttribute('rel') == "shortcut icon" || $link->getAttribute('rel') == "icon"){
			$ico = $link->getAttribute('href');
		}
	}
}

if(trim($ico) == ""){
	$ico = "images/default-favicon.png";
}

echo (String)$ico;
