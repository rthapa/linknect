<?php
include_once("../check_login_status.php");

if(isset($_POST['username']) && isset($_POST['listid'])){
	if($_POST['type'] == 'searchUsername'){
		$users = User::getFromSql("SELECT * FROM users WHERE username LIKE :username",
									array(':username' => '%'.$_POST['username'].'%'),$db);

		$response = array();
		foreach($users as $u){

			//$response[$u->getId()] = $u->getUsername();

			$isUserInGroup = UserGroup::getFromSql('SELECT * FROM usergroup WHERE userid = :userid AND listid = :listid',
							array(
								':userid'=>$u->getId(),
								':listid'=>$_POST['listid']
								), $db);

			$isInGroup = false;
			if(count($isUserInGroup) > 0){
				$isInGroup = true;
			}

			$userArr = array(
				'userid'=>$u->getId(),
				'username'=>$u->getUsername(),
				'isInGroup'=>$isInGroup
			);

			$response[] = $userArr;
		}

		$out = json_encode($response);
		echo $out;
		exit;
	}
}

if(isset($_POST['userid']) && isset($_POST['listid'])){
	if($_POST['type'] == 'toggleAddRemove'){
		$isUserInGroup = UserGroup::getFromSql('SELECT * FROM usergroup WHERE userid = :userid AND listid = :listid',
							array(
								':userid'=>$_POST['userid'],
								':listid'=>$_POST['listid']
								), $db);

		if(count($isUserInGroup) > 0){
			$user = $db->deleteRow('DELETE FROM usergroup WHERE userid = :userid AND listid = :listid',
										array(
											':userid'=>$_POST['userid'],
											':listid'=>$_POST['listid']
											)
										);
			echo 'deleted';
			exit;
		}else{
			$newUserGroup = new UserGroup;
			$newUserGroup->setUserId($_POST['userid']);
			$newUserGroup->setListId($_POST['listid']);
			$newUserGroup->save($db);
		
			if($newUserGroup->getId()){
				echo 'added';
				exit;
			}else{
				echo 'failed';
				exit;
			}
		}


	}
}
