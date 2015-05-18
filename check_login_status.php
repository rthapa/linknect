<?php
session_start();

// require '/home/m1alesis/public_html/'.basename(__DIR__).'/config.php';
// require '/home/m1alesis/public_html/'.basename(__DIR__).'/classes/PDOConnect.php';
// require '/home/m1alesis/public_html/'.basename(__DIR__).'/classes/BookmarkHelper.php';
// require '/home/m1alesis/public_html/'.basename(__DIR__).'/classes/Bookmark.php';
// require '/home/m1alesis/public_html/'.basename(__DIR__).'/classes/BookmarkList.php';
// require '/home/m1alesis/public_html/'.basename(__DIR__).'/classes/User.php';
// require '/home/m1alesis/public_html/'.basename(__DIR__).'/classes/Html.php';

require __DIR__.'/config.php';
require __DIR__.'/classes/PDOConnect.php';
require __DIR__.'/classes/BookmarkHelper.php';
require __DIR__.'/classes/Bookmark.php';
require __DIR__.'/classes/BookmarkList.php';
require __DIR__.'/classes/User.php';
require __DIR__.'/classes/UserGroup.php';
require __DIR__.'/classes/Html.php';

$db = new PDOConnect();

$user_ok = false;
$log_id = "";
$log_email = "";
$log_password = "";
// User Verify function
function evalLoggedUser($connection, $id,$e,$p){
	$sql = "SELECT COUNT(*) FROM users WHERE id='$id' AND email='$e' AND pwd='$p' AND activated='1' LIMIT 1";
    $query = $connection->query($sql);
    $numrows = count($query);
	if($numrows > 0){
		return true;
	}
}
if(isset($_SESSION["userid"]) && isset($_SESSION["email"]) && isset($_SESSION["password"])) {
	$log_id = preg_replace('#[^0-9]#', '', $_SESSION['userid']);
	$log_email = preg_replace('#[^a-z0-9]#i', '', $_SESSION['email']);
	$log_password = preg_replace('#[^a-z0-9]#i', '', $_SESSION['password']);
	// Verify the user
	$user_ok = evalLoggedUser($db, $log_id, $log_email, $log_password);
}else if(isset($_COOKIE["id"]) && isset($_COOKIE["user"]) && isset($_COOKIE["pass"])){
			$_SESSION['userid'] = preg_replace('#[^0-9]#', '', $_COOKIE['id']);
		    $_SESSION['email'] = preg_replace('#[^a-z0-9]#i', '', $_COOKIE['user']);
		    $_SESSION['password'] = preg_replace('#[^a-z0-9]#i', '', $_COOKIE['pass']);
			$log_id = $_SESSION['userid'];
			$log_email = $_SESSION['email'];
			$log_password = $_SESSION['password'];
			// Verify the user
			$user_ok = evalLoggedUser($db,$log_id,$log_email,$log_password);
			if($user_ok == true){
				// Update their lastlogin datetime field
				$sql = "UPDATE users SET lastlogin=now() WHERE id=:logid LIMIT 1";
		        $db->updateRow($sql, array(':logid'=>$log_id));
			}
}

