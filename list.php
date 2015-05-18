<?php
include_once("check_login_status.php");

$user = User::getFromId($log_id, $db);
// BookmarkHelper::updateReport('3', $db);

if(isset($_POST['bookmark_link'])){
	//get website original title for DB
	// $titles = \BookmarkHelper::getTitleFromWeb($_POST['bookmark_link']);
	// $original_title = "";
	// foreach($titles as $title){
	// 	$original_title = $title->nodeValue;
	// }

	//scrap title from the link
	$ch = curl_init($_POST['bookmark_link']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	// curl_setopt ($ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem");
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
	$site = $_POST['bookmark_link'];
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
	$ch = curl_init($_POST['bookmark_link']);
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
	$bookmarkObj = new Bookmark();
	$bookmarkObj->setOriginalTitle($titleScrapped);
	$bookmarkObj->setLink($_POST['bookmark_link']);
	$bookmarkObj->setSource(\BookmarkHelper::getSiteSource($_POST['bookmark_link']));
	$bookmarkObj->setIcon($iconScrapped);
	$bookmarkObj->setThumbnail($thumbnail);
	$bookmarkObj->setDescription($_POST['bookmark_description']);
	$bookmarkObj->setOwner($_SESSION['userid']);
	$bookmarkObj->save($db);

	$db->insertRow('INSERT INTO link (listid, bookmarkid) 
					values (:lid, :bid)', 
					array(":lid"=>$_GET['lid'],
						":bid" => $bookmarkObj->getId()
						)
					);
	header("Location: list.php?lid=".$_GET['lid']);
	exit;

}

//Update bookmark
if(isset($_POST['editbookmark_link'])){

	//get website original title for DB
	$titles = \BookmarkHelper::getTitleFromWeb($_POST['editbookmark_link']);
	$original_title = "";
	foreach($titles as $title){
		$original_title = $title->nodeValue;
	}

	$bookmarkObj = Bookmark::getFromId($_POST['bid'], $db);
	$bookmarkObj->setOriginalTitle($original_title);
	$bookmarkObj->setLink($_POST['editbookmark_link']);
	$bookmarkObj->setDescription($_POST['editbookmark_description']);
	$bookmarkObj->save($db);

	header("Location: list.php?lid=".$_GET['lid']);
	exit;
}
$hasBookmarks = 'false';
$listid = '';
$isOwnerTheViewer = false;
if(isset($_GET['lid'])){
	$listid = $_GET['lid'];
	$bookmarksList = BookmarkList::getFromId($_GET['lid'], $db);

	//check if list exists
	if(!$bookmarksList->getId()){
		header('Location: error.php');
		exit;
	}

	if($log_id == $bookmarksList->getOwner()){
		$isOwnerTheViewer = true;
	}else{
		if($bookmarksList->getPrivacy() != 'Open'){
			header('Location: error.php');
			exit;
		}
	}

}else{
	header('Location: error.php');
	exit;
}

?>
<?php
$html = new Html();
$html->title = $bookmarksList->getName();
$html->faviconUrl = 'favicon.ico';
$html->css[] = '<link rel="stylesheet" type="text/css" href="css/linknect.css">';
echo $html->injectHeader();
?>
<div class="overlayBg">
</div>
<div class="tooltip">
	<div class="tooltipOption"><span class="button closetooltip">close</span><div style="clear:both"></div></div>
	<p>
	You have no bookmarks. Click here to add a new bookmark.
	</p>
	<div style="background-color:#FFF5ED;padding: 5px;margin: -12px 17px 18px;">Bookmarks will be saved to the currently selected list. In this case the currently selected list is "<?=$bookmarksList->getName()?>"</div>
</div>
<?php if($user_ok):?>
	<div class="addUserToGroupDiv">
	<h4 class="addBookmarkFormHead">Add user to group</h4>
		<div class="addBookmarkForm">
			<input id="searchUsername" type="text" name="username" placeholder="Search username ... ">
			<div id="usernameDiv">
			</div>
			<div id="addBookmarkStatus" style="display:none;background-color:#A8A8A8;font-size: 0.9em; color:white;padding:5px; margin-bottom:5px">Bookmark link cannot be empty</div>
		</div>
	</div>
	<div class="addBookmarkDiv">
	<h4 class="addBookmarkFormHead">New Bookmark</h4>
		<form class="addBookmarkForm" id="bookmarkform" method="post" action="list.php?lid=<?=$_GET['lid']?>">
			<!-- <input type="text" name="bookmark_title" placeholder="Bookmark title"> -->
			<input id="bookmarklink" type="text" name="bookmark_link" placeholder="Bookmark link">
			<!-- <input type="text" name="bookmark_description" placeholder="Bookmark description"> -->
			<textarea id="bookmarkdesc" name="bookmark_description" placeholder="Bookmark notes.." class="addTextarea"></textarea>
			<br/>
			<div id="addBookmarkStatus" style="display:none;background-color:#A8A8A8;font-size: 0.9em; color:white;padding:5px; margin-bottom:5px">Bookmark link cannot be empty</div>
			<button class="button"><i class="fa fa-bookmark"></i> Save</button>
		</form>
	</div>
	<div class="editBookmarkDiv">
	<h4 class="addBookmarkFormHead">Edit Bookmark</h4>
		<form class="addBookmarkForm" id="editbookmarkform" method="post" action="list.php?lid=<?=$_GET['lid']?>">
			<input type="hidden" name="bid" id="editbookmarkid">
			<!-- <input type="text" name="bookmark_title" placeholder="Bookmark title"> -->
			<input id="editbookmarklink" type="text" name="editbookmark_link" placeholder="Bookmark link">
			<!-- <input type="text" name="bookmark_description" placeholder="Bookmark description"> -->
			<textarea id="editbookmarkdesc" name="editbookmark_description" placeholder="Bookmark notes.." class="addTextarea"></textarea>
			<br/>
			<div id="addBookmarkStatus" style="display:none;background-color:#A8A8A8;font-size: 0.9em; color:white;padding:5px; margin-bottom:5px">Bookmark link cannot be empty</div>
			<button class="button"><i class="fa fa-bookmark"></i> Update</button>
		</form>

	</div>

	<?php include_once('php_includes/sidebar.php'); ?>
<?php else:?>
	<div class="sidebar">
		<div class="userinfo">
			<a href="index.php"><button class="btn" style="margin-left:36px"><i class="fa fa-power-off"></i> Log in</button></a>
		</div>
		<div class="separator">
			<hr class="separator-bevel">
		</div>
	</div>
<?php endif;?>

<?php include_once('php_includes/head.php'); ?>

<div class="bodyWrapper">
	<div class="topInfoWrapper">
		<div class="bookmarkOptions" style="float:right">
			<?php if($user_ok && $isOwnerTheViewer):?>
				
				<button class="button" id="addUserBtn">Add User</button>
				<button style="margin-bottom:10px"class="button" id="eddBtn"><i class="fa fa-pencil"></i> Edit</button>
				<button style="margin-bottom:10px"class="button" id="addBtn"><i class="fa fa-bookmark" style="margin-right: 5px;"></i> Add</button>
			<?php endif;?>
				<button style="margin-bottom:10px"class="button" id="shareList"><i class="fa fa-share-alt" style="margin-right: 5px;"></i> Share</button>
		</div>
		<div class="listAbout">
			<div class="titleDiv">
				<h4><?=$bookmarksList->getName()?></h4>
			</div>
			<div class="authorDiv">
				<h5>by <span><?=User::getUsernameFromId($bookmarksList->getOwner(), $db)?></span></h5>
			</div>
			<div class="listPrivacy">
				<span><i class="fa <?=BookmarkList::getPrivacyIcon($bookmarksList->getPrivacy())?>"></i> <?=$bookmarksList->getPrivacy()?></span>
				<span><i class="fa fa-th"></i></span>
				<span><i class="fa fa-list-ul"></i></span>
			</div>
		</div>
		<div style="clear:both"></div>
		<!-- <div class="shareLinkDiv">
			<span style="float: left;
						padding: 6px;
						margin-right: 5px;
						background-color: #303030;">
				<i class="fa fa-share-alt" style="padding: 3px 7px 0 3px;"></i>
			</span>
			<h5 style="padding: 7px;background-color: #E2E2E2;">http://linknect.com/list.php?lid=<?=$listid?></h5>
			<div style="clear:both"></div>
		</div> -->
	</div>
	<div class="linkWrapper">
		<?php
			//error_log('COUNT ==> ');
			$bookmarks = Bookmark::getFromSql('SELECT * FROM bookmarks
										INNER JOIN link ON link.bookmarkid = bookmarks.bookmark_id
										WHERE link.listid = :listid ORDER BY UNIX_TIMESTAMP(bookmark_date) DESC', array(":listid"=> $listid), $db);
			if(count($bookmarks) > 0){
				$hasBookmarks = 'true';
			}else{
				$hasBookmarks = 'false';
			}
			foreach($bookmarks as $bookmark){
				?>
				<div class="bookmarkWrapperGrid">
					<div class="gridLeft">
						<!-- <div class="gridHeader">
							
						</div> -->
						<div class="gridContent">
							<h4><a href="<?=$bookmark->getLink()?>" target="_blank"><?=$bookmark->getOriginalTitle()?></a></h4>
							<div class="link"><a href="<?=$bookmark->getLink()?>" target="_blank"><?=(trim($bookmark->getSource()) != ''? $bookmark->getSource() : $bookmark->getLink())?></a></div>
							<p><?=$bookmark->getDescription()?></p>
							<!-- <button class="button">Visit</button> -->
						</div>
					</div>
					<div class="gridRight" 
					<?php if(trim($bookmark->getThumbnail() != '')){?>
					style="background: rgba(195, 195, 195, 0.81) url('<?=$bookmark->getThumbnail()?>');background-size: 200px 200px;background-repeat: no-repeat;
					background-size: cover;
					background-position: center;"
					<?php } ?>
					>
						<div class="iconDiv">
							<img width="30" src="<?=(trim($bookmark->getIcon()) != ''?$bookmark->getIcon() : \BookmarkHelper::getIcon($bookmark->getLink()))?>">
						</div>
						<div class="gridBookmarkOptions">
							<!-- <h4><?=(trim($bookmark->getSource()) != ''? $bookmark->getSource() : 'asldfjlaskjdgs')?></h4> -->
							<div class="gridEditIcon editBookmark" data-id="<?=$bookmark->getId()?>" style="margin-left: 37px;"><i class="fa fa-pencil"></i></div>
							<div class="gridEditIcon editBookmark" data-id="<?=$bookmark->getId()?>"><i class="fa fa-clock-o"></i></div>
							<div class="gridEditIcon deleteBookmark" data-id="<?=$bookmark->getId()?>"><i class="fa fa-trash-o"></i></div>
						</div>
					</div>
					<span style="clear:both;"></span>
				</div>
				<?php
			}
		?>
	</div>
</div>
<script>
var isAddFormActive = false;
var isEditFormActive = false;
var listType = '<?=$bookmarksList->getType()?>';
$(document).ready(function() {
	var hasBookmarks = '<?=$hasBookmarks?>';
	console.log(hasBookmarks);
	if(hasBookmarks == 'false'){
		if(listType == 'group'){
			$('.tooltip').css('left', '300px');
		}
		$('.tooltip').css('opacity', '1');
		$('.tooltip').css('top', '131px');
	}else{
		$('.tooltip').css('display', 'none');
	}
});
list.init();
sidebar.init();
// $('.closetooltip').click(function(){
// 	$($($(this).parent()).parent()).css('display', 'none');
// 	if($('.overlayBg').css('display') == 'block'){
// 		$('.overlayBg').css('display','none');
// 	}
// });
// $('.editBookmark').click(function(){
// 	var bookmarkid = $(this).data('id');

// 	$.ajax({
// 		url: "php_includes/edit.php",
// 		data: {bookmarkid: bookmarkid, type: 'editBookmark'},
// 		type: "POST"
// 	}).done(function(data) {
// 		//$( this ).addClass( "done" );
// 		console.log(data);
// 		if(data == "bad request"){
// 			window.location.href = 'error.php';
// 		}else{
// 			var jsonArr = JSON.parse(data);
// 			$('#editbookmarklink').val(jsonArr.blink);
// 			$('#editbookmarkdesc').val(jsonArr.bdesc);
// 			$('#editbookmarkid').val(bookmarkid);
// 		}
// 	});

// 	$('.editBookmarkDiv').css('display', 'block').center(true);
// 	isEditFormActive = true;
// 	$('.overlayBg').css('display', 'block');
// });

// $('#bookmarkform').submit(function() {
// 	if($.trim($('#bookmarklink').val()) == ""){
// 		$('#addBookmarkStatus').slideDown();
// 		return false;
// 	}
// });
$('.deleteBookmark').click(function(){
	var bid = $(this).data('id');
	var uid = <?=($user_ok)?$log_id:'0'?>;
	var lid= <?=$_GET['lid']?>;

	$.ajax({
		url: "php_includes/delete.php",
		data: { bid: bid, lid: lid, type: 'bookmark'},
		type: "POST"
	}).done(function(data) {
		//$( this ).addClass( "done" );
		if(data == "success"){
			window.location.href = 'list.php?lid=<?=$_GET["lid"]?>'; 
		}else if(data == "failed"){
			alert('failed');
			window.location.href = 'error.php';
		}
	});
});
// $('#shareList').click(function(){
// 	if($('.shareLinkDiv').css('display') == 'none'){
// 		$('.shareLinkDiv').slideDown();
// 	}else{
// 		$('.shareLinkDiv').slideUp();
// 	}
	
// });
// $('.overlayBg').click(function(){
// 	$('.overlayBg').css('display', 'none');
// 	$('.addBookmarkDiv').css('top', '-500%').css('display','none');
// 	$('.editBookmarkDiv').css('top', '-500%').css('display','none');
// 	$('.addUserToGroupDiv').css('top', '-500%').css('display','none');
// 	isAddFormActive = false;
// 	isEditFormActive = false;
// });

// $('#addBtn').click(function(){
// 	if($('.tooltip').css('display') == 'block') $('.tooltip').css('display', 'none');
// 	$('.overlayBg').css('display', 'block');
// 	$('.addBookmarkDiv').css('display', 'block').center(true);
// 	isAddFormActive = true;
// });

// $('#addUserBtn').click(function(){
// 	if($('.tooltip').css('display') == 'block') $('.tooltip').css('display', 'none');
// 	$('.overlayBg').css('display', 'block');
// 	$('.addUserToGroupDiv').css('display', 'block').center(true);
// 	isAddFormActive = true;
// });

$('#searchUsername').keyup(function(){
	if(this.value.length > 3){
		$.ajax({
			url: "php_includes/userAjax.php",
			data: { username: this.value,  listid: <?=$bookmarksList->getId()?>, type: 'searchUsername'},
			type: "POST",
			success: function(data){
				var html = '';
				$.each($.parseJSON(data), function(i, v){
					if(v['isInGroup']){
						html += '<h4 class="selectUsername" style="background-color:#B9C4DD" data-id="'+v['userid']+'"><i class="selUserBtn fa fa-check"></i> '+v['username']+'</h4>'
					}else{
						html += '<h4 class="selectUsername" data-id="'+v['userid']+'"><i class="selUserBtn fa fa-plus"></i> '+v['username']+'</h4>'
					}
				});
				$('#usernameDiv').html(html);
				console.log(data);
			}
		});
	}
});

$('body').on('click', '.selectUsername',function(){
	console.log('ok');
	console.log($(this).data('id'));
	$.ajax({
		url: "php_includes/userAjax.php",
		data: { userid: $(this).data('id'), listid: <?=$bookmarksList->getId()?>,type: 'toggleAddRemove'},
		type: "POST",
		success: function(data){
			if(data == 'deleted'){
				console.log('==>'+$(this).data('id'));
				//$(this).css('background-color', '#F9D0D0');
			}else if(data == 'added'){
				//$(this).css('background-color', '#B9C4DD');
			}else{
				window.location.href = 'error.php';
			}
		}
	});
});
// $(window).scroll(function(){
// 	if(isAddFormActive){
// 		$('.addBookmarkDiv').center(true);
// 	}

// 	if(isEditFormActive){
// 		$('.editBookmarkDiv').center(true);
// 	}
// });


</script>
<?php
echo $html->injectFooter();
