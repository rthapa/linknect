<?php
include_once("../check_login_status.php");

if(isset($_POST["checkemail"])){
	$email =$_POST['checkemail'];

	$emailExist = User::emailExist($email, $db);

	if($emailExist){
		$isavailable = 'taken';
	}else{
		$isavailable = 'available';
	}

	$respond = array("data" => $isavailable);
	$out = json_encode($respond);
	echo $out;
	exit();
}

if(isset($_POST["checkusername"])){
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['checkusername']);
	$usernameExist = User::usernameExist($username, $db);

	if($usernameExist){
		$isavailable = 'taken';
	}else{
		$isavailable = 'available';
	}

	$respond = array("data" => $isavailable);
	$out = json_encode($respond);
	echo $out;
	exit();
}

if(isset($_POST['password'])){
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['username']);
	$email = $_POST['email'];
	$password = $_POST['password'];
	$repassword = $_POST['repassword'];
	if($username == "" || $email == "" || $password == "" || $repassword == ""){
		header("location: error.php?type=104");
		exit();
	}

	if($password != $repassword){
		header("location: error.php?type=104");
		exit();
	}

	//check email availability 
	$emailExist = User::emailExist($email, $db);

	if($emailExist){
		header("location: error.php?type=104");
		exit();
	}

	//check username availability 
	$usernameExist = User::usernameExist($username, $db);

	if($usernameExist){
		header("location: error.php?type=104");
		exit();
	}

	$password = md5($password);
	$ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// $db->insertRow('INSERT INTO users (user_email, user_pwd, activated, ip, lastlogin, username) 
	// 							VALUES (:email, :password, :activated, :ip, now(), :username)',
	// 							array(":email"=>$email,
	// 									":password"=>$password,
	// 									":activated"=>'1',
	// 									":ip"=>$ip,
	// 									":username"=>$username));

	$user = new User();
	$user->setEmail($email);
	$user->setUsername($username);
	$user->setPassword($password);
	$user->setIp($ip);
	$user->setActivated('1');
	$user->save($db);


	//create their session and cookies and log them in
	// $userid = $db->getLastInsertId();
	$userid = $user->getId();
	$_SESSION['userid'] = $userid;
	$_SESSION['email'] = $email;
	$_SESSION['password'] = $password;
	setcookie("id", $userid, strtotime( '+30 days' ), "/", "", "", TRUE);
	setcookie("user", $email, strtotime( '+30 days' ), "/", "", "", TRUE);
	setcookie("pass", $password, strtotime( '+30 days' ), "/", "", "", TRUE); 
	header('location: ../dash.php');
	exit();
}


//http://stackoverflow.com/questions/19596402/disable-form-submit-until-fields-have-been-validated-using-jquery
