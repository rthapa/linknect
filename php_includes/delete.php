<?php
include_once("../check_login_status.php");

if(isset($_POST['bid']) && isset($_POST['type'])){
	if($_POST['type'] == 'bookmark'){

		$queryDeleteBookmark = \BookmarkHelper::deleteBookmark($_POST['bid'], $log_id, $db);

		if($queryDeleteBookmark){
			echo 'success';
		}else{
			echo 'failed';
		}
		exit;
	}
}

if(isset($_POST['lid']) && isset($_POST['type'])){
	if($_POST['type'] == 'list'){
		
		$queryDeleteList = \BookmarkHelper::deleteList($_POST['lid'], $log_id, $db);

		if($queryDeleteList){
			echo 'success';
		}else{
			echo 'failed';
		}
		exit;
	}
}
