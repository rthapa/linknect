<?php
include_once("check_login_status.php");

// $db = new PDOConnect();

// $query = $db->query('SELECT * FROM bookmarks');

// foreach($query as $q){
// 	// echo $q[].'<br/>';
// 	echo $q['bookmark_id'];
// 	echo $q['bookmark_link'];
// 	echo '<pre>', print_r($q), '</pre>';
	
// }
//echo '<pre>', print_r($query), '</pre>';

// $r2 = $query->fetchALL(PDO::FETCH_ASSOC);
// echo '<pre>', print_r($r2), '</pre>';


// function getfavicon($url){
//     $favicon = '';
//     $html = file_get_contents($url);
//     $dom = new DOMDocument();
//     $dom->loadHTML($html);
//     $links = $dom->getElementsByTagName('link');
//     for ($i = 0; $i < $links->length; $i++){
//         $link = $links->item($i);
//         if($link->getAttribute('rel') == 'icon'){
//             $favicon = $link->getAttribute('href');
// 	}
//     }
//     return $favicon;
// }
// $website = "http://cloudnnect.com";
// $favicon = getfavicon($website);
// echo $favicon;
// echo '<img src="http://www.'.$website.'/'.$favicon.'">';

/******************************/
// $ch = curl_init("http://psychology.about.com/od/apastyle/p/labreport.htm");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt ($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
// $cl = curl_exec($ch);

// $dom = new DOMDocument();
// @$dom->loadHTML($cl);

// $title = $dom->getElementsByTagName("title");
// // echo "<pre>";
// // print_r($title);
// foreach($title as $link){
// 	echo $link->nodeValue;
// }
/******************************/


/******************************/
// $ch = curl_init("http://stackoverflow.com/");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt ($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
// $cl = curl_exec($ch);

// $dom = new DOMDocument();
// @$dom->loadHTML($cl);

// $title = $dom->getElementsByTagName("link");
// // echo "<pre>";
// // print_r($title);
// foreach($title as $link){
// 	if($link->getAttribute('rel') == 'icon'){
// 		echo '<img src="'.$link->getAttribute('href').'">';
// 	}else{
// 		$exp = explode(" ",$link->getAttribute('rel'));
// 		foreach($exp as $e){
// 			if($ecc
// 		}
// 	}
// }
/******************************/

// $ch = curl_init("https://www.youtube.com/watch?v=V3voCgKsae8");
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt ($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
// $cl = curl_exec($ch);

// $dom = new DOMDocument();
// @$dom->loadHTML($cl);

// $title = $dom->getElementsByTagName("link");
// // echo "<pre>";
// print_r($title);
// foreach($title as $link){
// 	$exp = explode(" ",$link->getAttribute('rel'));

// 	foreach($exp as $e){
// 		echo $e.'<br>';
// 		if($e == 'icon'){
// 			echo '<img src="'.$link->getAttribute('href').'">';
// 		}
// 	}
// }

/*******************/
// $titles = \BookmarkHelper::getTitleFromWeb("https://www.facebook.com/brentrivera?fref=nf");

// foreach($titles as $title){
// 	echo $title->nodeValue;
// }
/*********************/
$blist = BookmarkList::getFromId('11', $db);
	if($blist->getId()){
		echo 'found';
	}else{
		echo 'error';
	}













//not workigng title and icon
//https://dribbble.com/shots/1371032-todo-app-notification?list=searches&tag=todo_app&offset=236
?>

