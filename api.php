<?php
session_start();
header('Access-Control-Allow-Origin: *');
require __DIR__.'/config.php';
require __DIR__.'/classes/PDOConnect.php';
require __DIR__.'/classes/BookmarkHelper.php';
require __DIR__.'/classes/Bookmark.php';
require __DIR__.'/classes/BookmarkList.php';
require __DIR__.'/classes/User.php';
//require __DIR__.'/classes/UserGroup.php';
require __DIR__.'/classes/Html.php';

$db = new PDOConnect();
$out= array();

/**
 * GET variables
 */
if(isset($_GET['user']) && isset($_GET['password']) && isset($_GET['type'])){
	/**
	 * Get users list
	 */
	if($_GET['type'] == 'getList'){
		$user = User::getFromAuth($_GET['user'], $_GET['password'], $db);

		//echo json_encode(array('username' => $user->getUsername()));
		if($user){
			//fetch all list
			$allLists = BookmarkList::getFromSql('SELECT * FROM list WHERE list_owner = :listowner',array(':listowner' => $user->getId()),$db);
			$listArr = array();
			if(count($allLists) > 0 ){
				foreach($allLists as $list){
					$listArr[] = array(
							'totalBookmark' => BookmarkList::getTotalBookmarksFromListId($list->getId(), $db),
							'name' => $list->getName(),
							'id' => $list->getId()
						);
				}
				echo json_encode($listArr);
				exit;
			}else{
				echo json_encode(array('status' => 'no list'));
			}
		}else{
			echo json_encode(array('status' => 'user auth failed'));
		}
	}

	/**
	 * get Bookmarks of a list
	 */
	if($_GET['type'] == 'getBookmarks' && isset($_GET['lid'])){
		$lid = $_GET['lid'];
		if(trim($lid) != ''){
			if(!is_numeric($lid)){
				echo json_encode(array('status1' => 'invalid list'));
				exit;
			}
			//echo json_encode(array('status' => 'lid true'));
			$bookmarks = Bookmark::getFromSql('SELECT * FROM bookmarks
										INNER JOIN link ON link.bookmarkid = bookmarks.bookmark_id
										WHERE link.listid = :listid ORDER BY UNIX_TIMESTAMP(bookmark_date) DESC', array(":listid"=> $lid), $db);

			if(count($bookmarks) > 0){
				$bookmarkArr = array();
				foreach($bookmarks as $bookmark){
					$bookmarkArr[] = array(
										'title'=>$bookmark->getOriginalTitle(),
										'link'=>$bookmark->getLink(),
										'icon'=> BookmarkHelper::getIcon($bookmark->getLink()),
										'id'=>$bookmark->getId(),
										'date'=>date("d M y",strtotime($bookmark->getDate())),
										'description'=>$bookmark->getDescription(),
										'listName'=>BookmarkList::getListNameFromId($lid, $db)
										);
				}
				echo json_encode($bookmarkArr);
				exit;
			}else{
				echo json_encode(array('status' => 'no bookmarks', 'listName' => BookmarkList::getListNameFromId($lid, $db)));
				exit;
			}
		}else{
			echo json_encode(array('status2' => 'invalid list'));
			exit;
		}
	}

	/**
	 * GET list for edit
	 */
	if($_GET['type'] == 'getEditList' && isset($_GET['lid'])){
		$lid = $_GET['lid'];
		if(trim($lid) != ''){
			$list = BookmarkList::getFromId($_GET['lid'], $db);

			if(!$list->getId()){
				echo json_encode(array('status' => 'list id not found'));
				exit;
			}

			$user = User::getFromAuth($_GET['user'], $_GET['password'], $db);
			if($user){
				if($user->getId() != $list->getOwner()){
					echo json_encode(array('status' => 'user is not authorized to edit this list'));
					exit;
				}

				$listArr = array(
								'name' => $list->getName(),
								'id' => $list->getId(),
								'privacy' => $list->getPrivacy(),
								'description'=>$list->getDescription()
							);


				echo json_encode($listArr);
				exit;
			}else{
				echo json_encode(array('status' => 'user validation failed'));
				exit;
			}
		}else{
			echo json_encode(array('status' => 'invalid list id'));
			exit;
		}
	}

	/**
	 * GET bookmark for edit
	 */
	if($_GET['type'] == 'getEditBookmark' && isset($_GET['bid'])){
		$bid = $_GET['bid'];
		if(trim($bid) != ''){
			$bookmark = Bookmark::getFromId($_GET['bid'], $db);

			if(!$bookmark->getId()){
				echo json_encode(array('status' => 'bookmark id not found'));
				exit;
			}

			$user = User::getFromAuth($_GET['user'], $_GET['password'], $db);
			if($user){
				if($user->getId() != $bookmark->getOwner()){
					echo json_encode(array('status' => 'user is not authorized to edit this bookmark'));
					exit;
				}

				$bookmarkArr = array(
								'title'=>$bookmark->getOriginalTitle(),
								'link'=>$bookmark->getLink(),
								'icon'=> BookmarkHelper::getIcon($bookmark->getLink()),
								'id'=>$bookmark->getId(),
								//'date'=>date("d M y",strtotime($bookmark->getDate())),
								'description'=>$bookmark->getDescription(),
								'listName'=>BookmarkList::getListNameFromId($lid, $db)
							);


				echo json_encode($bookmarkArr);
				exit;
			}else{
				echo json_encode(array('status' => 'user validation failed'));
				exit;
			}
		}else{
			echo json_encode(array('status' => 'invalid bookmark id'));
			exit;
		}
	}
}

/**
 * POST variables
 */
if(isset($_POST['user']) && isset($_POST['password']) && isset($_POST['type'])){
	/**
	 * login user
	 */
	// echo json_encode(array('status' => 'GET success'));
	// exit;
	if($_POST['type'] == 'login'){
		$user = User::getFromAuth($_POST['user'], $_POST['password'], $db);
		if($user){
			echo json_encode(array('status' => 'success',
									'username' => $user->getUsername(),
									'email' => $user->getEmail()
									));
			exit;
		}else{
			echo json_encode(array('status' => 'failed'));
			exit;
		}
	}

	/**
	 * Save list 
	 */
	if($_POST['type'] == 'saveList'){
		if(isset($_POST['name']) && 
			isset($_POST['description']) && 
			isset($_POST['privacy']) && 
			isset($_POST['agent'])){
				$user = User::getFromAuth($_POST['user'], $_POST['password'], $db);
				if(!$user){
					echo json_encode(array('status' => 'user auth failed'));
					exit;
				}

			$newList = new BookmarkList();
			$newList->setName($_POST['name']);
			$newList->setDescription($_POST['description']);
			$newList->setOwner($user->getId());
			$newList->setPrivacy($_POST['privacy']);
			$newList->save($db);
			echo json_encode(array('status' => 'success'));
			exit;
		}else{
			echo json_encode(array('status' => 'expected token not passed'));
			exit;
		}
	}

	/**
	 * Edit list 
	 */
	if($_POST['type'] == 'editList'){
		if(isset($_POST['name']) && 
			isset($_POST['description']) && 
			isset($_POST['privacy']) && 
			isset($_POST['lid']) && 
			isset($_POST['agent'])){
				$user = User::getFromAuth($_POST['user'], $_POST['password'], $db);
				if(!$user){
					echo json_encode(array('status' => 'user auth failed'));
					exit;
				}

			// $newList = new BookmarkList();
			// $newList->setName($_POST['name']);
			// $newList->setDescription($_POST['description']);
			// $newList->setOwner($user->getId());
			// $newList->setPrivacy($_POST['privacy']);
			// $newList->save($db);

			$updateList = BookmarkList::getFromId($_POST['lid'], $db);
			$updateList->setName($_POST['name']);
			$updateList->setDescription($_POST['description']);
			$updateList->setPrivacy($_POST['privacy']);
			$updateList->save($db);
			echo json_encode(array('status' => 'success'));
			exit;
		}else{
			echo json_encode(array('status' => 'expected token not passed'));
			exit;
		}
	}

	/**
	 * Edit bookmark 
	 */
	if($_POST['type'] == 'editBookmark'){
		if(isset($_POST['link']) && 
			isset($_POST['description']) && 
			isset($_POST['bid']) && 
			isset($_POST['agent'])){
				$user = User::getFromAuth($_POST['user'], $_POST['password'], $db);
				if(!$user){
					echo json_encode(array('status' => 'user auth failed'));
					exit;
				}

			$titles = \BookmarkHelper::getTitleFromWeb($_POST['link']);
			$original_title = "";
			foreach($titles as $title){
				$original_title = $title->nodeValue;
			}

			$bookmarkObj = Bookmark::getFromId($_POST['bid'], $db);
			$bookmarkObj->setOriginalTitle($original_title);
			$bookmarkObj->setLink($_POST['link']);
			$bookmarkObj->setDescription($_POST['description']);
			$bookmarkObj->save($db);

			echo json_encode(array('status' => 'success'));
			exit;
		}else{
			echo json_encode(array('status' => 'expected token not passed'));
			exit;
		}
	}

	/**
	 * Save bookmark 
	 */
	if($_POST['type'] == 'saveBookmark'){
		if(isset($_POST['lid']) && 
			isset($_POST['url']) && 
			isset($_POST['favicon']) && 
			isset($_POST['title']) &&
			isset($_POST['description']) &&
			isset($_POST['agent'])){
				//check user exist and get user object
				$user = User::getFromAuth($_POST['user'], $_POST['password'], $db);
				if($user){
					$list = BookmarkList::getFromId($_POST['lid'], $db);
					//check if list exists
					if(!$list->getId()){
						echo json_encode(array('status' => 'list with the given id does not exist'));
						exit;
					}

					// echo json_encode(array('lid' => $list->getOwner(), 'userid' => $user->getId()));
					// exit;
					//check if user is the owner of the list
					if($list->getOwner() != $user->getId()){
						echo json_encode(array('status' => 'permission denied to add bookmark'));
						exit;
					}

					$titles = \BookmarkHelper::getTitleFromWeb($_POST['url']);
					$original_title = "";
					foreach($titles as $title){
						$original_title = $title->nodeValue;
					}
	
					$bookmarkObj = new Bookmark();
					$bookmarkObj->setOriginalTitle($original_title);
					$bookmarkObj->setLink($_POST['url']);
					$bookmarkObj->setDescription($_POST['description']);
					$bookmarkObj->setOwner($user->getId());
					//$bookmarkObj->setIcon($_POST['url']);
					$bookmarkObj->save($db);

					$db->insertRow('INSERT INTO link (listid, bookmarkid) 
									values (:lid, :bid)', 
									array(":lid"=>$_POST['lid'],
										":bid" => $bookmarkObj->getId()
										)
									);
					echo json_encode(array('status' => 'success'));
					exit;
				}else{
					echo json_encode(array('status' => 'user auth failed'));
					exit;
				}
				echo json_encode($_POST);
				exit;
		}else{
			echo json_encode(array('status' => 'expected token not passed'));
			exit;
		}
	}

	/**
	 * delete list
	 */
	if($_POST['type'] == 'deleteList'){
		if(!isset($_POST['lid'])){
			echo json_encode(array('status' => 'expected token not passed'));
			exit;
		}

		$user = User::getFromAuth($_POST['user'], $_POST['password'], $db);
		if(!$user){
			echo json_encode(array('status' => 'user auth failed'));
			exit;
		}

		$queryDeleteList = \BookmarkHelper::deleteList($_POST['lid'], $user->getId(), $db);

		if($queryDeleteList){
			echo json_encode(array('status' => 'success'));
			exit;
		}else{
			echo json_encode(array('status' => 'delete failed'));
			exit;
		}
		exit;
	}

	/**
	 * Delete bookmark
	 */
	if($_POST['type'] == 'deleteBookmark'){
		if(!isset($_POST['bid'])){
			echo json_encode(array('status' => 'expected token not passed'));
			exit;
		}

		$user = User::getFromAuth($_POST['user'], $_POST['password'], $db);
		if(!$user){
			echo json_encode(array('status' => 'user auth failed'));
			exit;
		}

		$queryDeleteBookmark = \BookmarkHelper::deleteBookmark($_POST['bid'], $user->getId(), $db);

		if($queryDeleteBookmark){
			echo json_encode(array('status' => 'success'));
			exit;
		}else{
			echo json_encode(array('status' => 'query failed'));
			exit;
		}
		exit;
	}
}


