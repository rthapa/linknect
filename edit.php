<?php
include_once("check_login_status.php");
$user = User::getFromId($log_id, $db);

if(empty($_GET['uid'])){
	header('Location: error.php');
	exit;
}


$html = new Html();
$html->title = 'Edit user';
$html->faviconUrl = 'favicon.ico';
$html->css[] = '<link rel="stylesheet" type="text/css" href="css/linknect.css">';
echo $html->injectHeader();
?>

<?php include_once('php_includes/head.php'); ?>
<?php include_once('php_includes/sidebar.php'); ?>
<div class="bodyWrapper">
	<div class="topInfoWrapper" style="padding:0px">
		<div class="titleDiv" style="padding:20px">
			<!-- <div class="userAvatar">
				<img src="images/avatar.png">
				<h4><?=$user->getUsername();?></h4>
				<div class="userStats userStatsRight">
					<span class="total"><?=BookmarkList::getTotalListFromUserId($user->getId(), $db)?></span>
					<span class="name">Lists</span>
				</div>
				<div class="userStats userStatsLeft">
					<span class="total"><?=Bookmark::getTotalBookmarksFromUserId($user->getId(), $db)?></span>
					<span class="name">Bookmarks</span>
				</div>
			</div> -->
			<h4>Edit - <?=$user->getUsername()?></h4>
		</div>
		<div class="editNav">
			<ul>
				<li class="editNavAcive">Profile</li>
				<li>Password</li>
			</ul>
		</div>
	</div>
	<div class="contentbox">
		<div class="userAvatar">
			<img src="images/avatar.png">
			<div class="file-upload">
			<div class="file-select">
				<div class="file-select-button" id="fileName">Choose File</div>
					<div class="file-select-name" id="noFile">No file chosen...</div> 
					<input type="file" name="chooseFile" id="chooseFile">
				</div>
			</div>
		</div>
		<div style="text-align:center;">
			<input type="text" placeholder="<?=($user->getName()!= '' ? $user->getName() : 'Name')?>">
			<input type="text" placeholder="<?=($user->getEmail()!= '' ? $user->getEmail() : 'Email')?>">
			<input type="text" placeholder="Location">
		</div>
	</div>
</div>
<script>
	sidebar.init();
$('#chooseFile').on('change', function () {
	var filename = $("#chooseFile").val();
	if ($.trim(filename) == '') {
		$(".file-upload").removeClass('active');
		$("#noFile").text("No file chosen..."); 
	}
	else {
		$(".file-upload").addClass('active');
		$("#noFile").text(filename.replace("C:\\fakepath\\", "")); 
	}
});
</script>
