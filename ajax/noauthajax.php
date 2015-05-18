<?php
include_once("../check_login_status.php");

if(!isset($_POST['type'])){
 //bad request
}

if(isset($_POST['email']) && isset($_POST['password']) && $_POST['type'] == 'login'){

	$user = User::getFromAuth($_POST['email'], $_POST['password'], $db);

	if(!$user){
		//set the status to login failed
		$out = array('status' => 'failed');
	}else{
		//user found
		//set the session and cookies
		$_SESSION['userid'] = $user->getId();
		$_SESSION['email'] = $user->getEmail();
		$_SESSION['password'] = $user->getPassword();
		setcookie("id", $user->getId(), strtotime( '+30 days' ), "/", "", "", TRUE);
		setcookie("user", $user->getEmail(), strtotime( '+30 days' ), "/", "", "", TRUE);
		setcookie("pass", $user->getPassword(), strtotime( '+30 days' ), "/", "", "", TRUE); 

		//update the user. for eg: lastlogin = now(), ip etc;
		$user->save($db);

		//set the status to success
		$out = array('status' => 'success');
	}

	//send the response to ajax and exit this script
	echo json_encode($out);
	exit();
}
