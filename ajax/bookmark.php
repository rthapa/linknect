<?php
include_once("../check_login_status.php");

if(isset($_POST['type'])){
	if($_POST['type'] == 'saveBookmark'){
		//scrap title from the link
		$ch = curl_init($_POST['link']);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt ($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
		$cl = curl_exec($ch);

		$dom = new DOMDocument();
		@$dom->loadHTML($cl);

		$title = $dom->getElementsByTagName("title");
		// echo "<pre>";
		// print_r($title);
		$t = '';
		foreach($title as $link){
			$t = $link->nodeValue;
		}

		error_log($t);
		
		echo json_encode(array('test' => $t));
	}
}

if(isset($_GET['type'])){
	if($_GET['type'] == '' && !empty($_GET['bookmark_link'])){
		$titles = \BookmarkHelper::getTitleFromWeb($_POST['bookmark_link']);
	}
}
