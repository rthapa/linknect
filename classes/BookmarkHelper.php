<?php
class BookmarkHelper{

public static function updateReport($id, $connection){

	$pageReport = self::getPageReport($id, $connection);
	$views = (int)$pageReport[0]['views'];
	$views = $views + 1;
	$connection->updateRow('UPDATE pagereport SET views = :views WHERE id = :id',
								array(':views' => $views,
									':id' => $id));
}

public static function getPageReport($id, $connection){
	$query = $connection->query('SELECT * FROM pagereport
							WHERE id =:id LIMIT 1',
							array(":id"=>$id)) ;
	return $query;
}
/**
 * Deletes a bookmark from the DB and its link with the list
 * @param  [type] $bid        [bookmark id]
 * @param  [type] $uid        [user id for authentication]
 * @param  [type] $connection [PDO handle]
 * @return [type]             [isSuccess]
 */
public static function deleteBookmark($bid, $uid, $connection){
	//check if id exists and auth
	$query = $connection->query('SELECT * FROM bookmarks
							WHERE bookmark_id = :id AND
							bookmark_owner = :bowner LIMIT 1',
							array(":id"=>$bid, "bowner"=>$uid)) ;
	if(count($query) < 1){
		return false;
		exit;
	}
	//delete bookmark query
	$query1 = $connection->deleteRow("DELETE FROM bookmarks
							WHERE bookmark_id = :bid AND 
							bookmark_owner = :uid",
							array(":bid" => $bid, ":uid" => $uid));
	//delete link query
	$query2 = $connection->deleteRow("DELETE FROM link
						WHERE bookmarkid = :bid", array(
						":bid" => $bid
						));
	return true;
}

public static function deleteList($lid, $uid, $connection){
	//check if id exists and auth 
	$query = $connection->query('SELECT * FROM list
							WHERE id = :id AND
							list_owner = :lowner LIMIT 1',
							array(":id"=>$lid, ":lowner"=>$uid)) ;
	if(count($query) < 1){
		return false;
		exit;
	}
	//delete all bookmarks inside this list
	$queryAllBookmarks = $connection->query('SELECT * FROM bookmarks
							INNER JOIN link ON link.bookmarkid = bookmarks.bookmark_id
							WHERE link.listid = :lid',
							array(":lid"=>$lid)) ;
	foreach($queryAllBookmarks as $bookmark){
		self::deleteBookmark($bookmark['bookmark_id'], $uid, $connection);
	}
	//delete the list
	$queryDeleteList = $connection->deleteRow("DELETE FROM list WHERE id = :lid", 
									array(":lid"=>$lid)) ;
	return true;
}

/**
 * Gets a column data of a row from List DB
 * @param  [type] $id         [list id]
 * @param  [type] $columnName [column name to fetch data from]
 * @param  [type] $connection [PDO handle]
 * @return [type]             [column data]
 */
public static function getListColumn($id, $columnName, $connection){
	$query = $connection->query('SELECT * FROM list
							WHERE list.id = :id LIMIT 1',
							array(":id"=>$id)) ;
	$list_column="";
	foreach($query as $q){
		$list_column = $q[$columnName];
	}
	return $list_column;
}

public static function getListFromId($id, $connection){
	$query = $connection->query('SELECT * FROM list
							WHERE list.id =:id LIMIT 1',
							array(":id"=>$id)) ;
	return $query;
}

public static function getBookmarkFromId($id, $connection){
	$query = $connection->query('SELECT * FROM bookmarks
							WHERE bookmark_id =:id LIMIT 1',
							array(":id"=>$id)) ;
	return $query;
}
/**
 * Harvests the title tag from a website
 * @param  [string] web address
 * @return [array] returns array of $dom elements by tag name "title"
 */
public static function getTitleFromWeb($website){
	$ch = curl_init($website);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
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
	return $t;
}

public static function getSiteSource($site){
	$siteExplode = explode("/",$site);
	//var_dump($siteExplode);
	//echo "<br /> array no:".$siteExplode[0]."<br/>";
	$link = $siteExplode[0].$siteExplode[1].$siteExplode[2];

	$finalLinkForIco = $siteExplode[1].$siteExplode[2];

	return $finalLinkForIco;
}
/**
 * Retrieve the ico file of a webpage
 * @param  [type] $site [web url]
 * @return [type]       [href of ico]
 */
public static function getIcon($site){
	$siteExplode = explode("/",$site);
	//var_dump($siteExplode);
	//echo "<br /> array no:".$siteExplode[0]."<br/>";
	$link = $siteExplode[0].$siteExplode[1].$siteExplode[2];

	$finalLinkForIco = $siteExplode[0].'//'.$siteExplode[1].$siteExplode[2];
	//echo "<br />-->".$finalLinkForIco."<--<br />";

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
		//echo $link->nodeValue;
		//print_r($title);

		foreach($title as $link){
			if($link->getAttribute('rel') == "shortcut icon" || $link->getAttribute('rel') == "icon"){
				$ico = $link->getAttribute('href');
			}
		}
	}

	if(trim($ico) == ""){
		$ico = "images/default-favicon.png";
	}
	//echo '<br />Returned: '.$ico.'<br />';
	return (String)$ico;
}


}

