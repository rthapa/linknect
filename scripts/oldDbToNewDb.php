<?php
ini_set('max_execution_time', 600000);
include_once("../check_login_status.php");

$query = $db->query('SELECT * FROM bookmarks');
foreach($query as $q){
	$bookmark = Bookmark::getFromId($q['bookmark_id'], $db);

	
		echo $q['bookmark_id'].' => ';
		echo $q['bookmark_link'].'<br>';
		// scrap title from the link
		$ch = curl_init($bookmark->getLink());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
		$cl = curl_exec($ch);

		$dom = new DOMDocument();
		@$dom->loadHTML($cl);

		$title = $dom->getElementsByTagName("title");
		// echo "<pre>";
		// print_r($title);
		$titleScrapped = '';
		foreach($title as $link){
			$titleScrapped = $link->nodeValue;
		}

		//scrap icon
		$site = $bookmark->getLink();
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

		$iconScrapped = (String)$ico;

		//scrap thumbnail
		$ch = curl_init($bookmark->getLink());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
		$cl = curl_exec($ch);

		$dom = new DOMDocument();
		@$dom->loadHTML($cl);

		$title = $dom->getElementsByTagName("meta");
		// echo "<pre>";
		// print_r($title);
		
		// $t = '';
		$thumbnail = '';
		foreach($title as $link){
			// echo '=>'.$link->getAttribute('property');
			if($link->getAttribute('property') == 'og:image'){
				$thumbnail = $link->getAttribute('content');
			}
		}

		//write to db
		$bookmark->setOriginalTitle($titleScrapped);
		$bookmark->setLink($bookmark->getLink());
		$bookmark->setSource(\BookmarkHelper::getSiteSource($bookmark->getLink()));
		$bookmark->setIcon($iconScrapped);
		$bookmark->setThumbnail($thumbnail);
		$bookmark->setDescription($bookmark->getDescription());
		$bookmark->setOwner($bookmark->getOwner());
		$bookmark->save($db);
	
	
}
