<?php
include_once("../check_login_status.php");

if(isset($_POST['lid']) && isset($_POST['type'])){
	if($_POST['type'] == 'editlist'){
		
		$list = \BookmarkHelper::getListFromId($_POST['lid'],$db);
		if(count($list) < 1){
			echo 'bad request';
			exit;
		}

		if($list[0]['list_owner'] != $log_id){
			echo 'bad request';
			exit;
		}


		$response = array();
		$response['listname'] = $list[0]['list_name'];
		$response['listdesc'] = $list[0]['list_description'];
		$response['listprivacy'] = $list[0]['list_privacy'];
		$response['listtype'] = $list[0]['list_type'];

		$out = json_encode($response);
		echo $out;
		exit;
	}
}

if(isset($_POST['bookmarkid']) && isset($_POST['type'])){
	if($_POST['type'] == 'editBookmark'){
		$bookmark = \BookmarkHelper::getBookmarkFromId($_POST['bookmarkid'],$db);
		if(count($bookmark) < 1){
			echo 'bad request';
			exit;
		}

		if($bookmark[0]['bookmark_owner'] != $log_id){
			echo 'bad request';
			exit;
		}


		$response = array();
		$response['btitle'] = $bookmark[0]['bookmark_original_title'];
		$response['blink'] = $bookmark[0]['bookmark_link'];
		$response['bdesc'] = $bookmark[0]['bookmark_description'];

		$out = json_encode($response);
		echo $out;

		//echo $bookmark[0]['bookmark_link'];
		exit;
	}

}

