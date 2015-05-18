<?php
include_once("check_login_status.php");

//BookmarkHelper::updateReport('2', $db);
if(!$user_ok){
	header("location: index.php");
	exit();
}

$user = User::getFromId($log_id, $db);

//Insert new LIST!
if(isset($_POST['list_name'])){
	error_log(var_export($_POST, true));
	$newList = new BookmarkList();
	$newList->setName($_POST['list_name']);
	$newList->setDescription($_POST['list_description']);
	$newList->setOwner($log_id);
	$newList->setPrivacy($_POST['list_privacy']);
	$newList->setType($_POST['list_type']);
	$newList->save($db);
	header("location: dash.php");
}

//Update list
if(isset($_POST['editlist_name'])){
	$updateList = BookmarkList::getFromId($_POST['listid'], $db);
	$updateList->setName($_POST['editlist_name']);
	$updateList->setDescription($_POST['editlist_description']);
	$updateList->setPrivacy($_POST['editlist_privacy']);
	$updateList->save($db);
	header("location: dash.php");
}

$hasList = 'true';
?>
<?php
$html = new Html();
$html->title = 'Dash';
$html->faviconUrl = 'favicon.ico';
$html->css[] = '<link rel="stylesheet" type="text/css" href="css/linknect.css">';
echo $html->injectHeader();
?>
<!-- <body style="background-color:#F1F1F1; position:relative;"> -->
<div class="confirmBox">
	<div style="padding:15px">
		<p>Deleting the list will also delete the bookmarks in it. Are you sure you want to delete this list?</p>
	</div>
	<div class="tooltipOption">
		<span class="button closetooltip" style="margin-left:15px">Cancel</span>
		<span class="button confirmbtn">Delete</span>
	<div style="clear:both"></div>
	</div>
</div>

<div class="tooltip">
	<div class="tooltipOption"><span class="button closetooltip">close</span><div style="clear:both"></div></div>
	<p>
	You have no list. Click here to create a new list.
	</p>
	<div style="background-color:#FFF5ED;
padding: 5px;
margin: -12px 17px 18px;">A list is a collection of bookmarks. Once created, you will be able to add bookmarks to it.</div>
</div>
<div class="overlayBg">
</div>
<div class="addBookmarkDiv">
	<h4 class="addBookmarkFormHead">New List</h4>
	<form class="addBookmarkForm" id="listform" method="post" action="dash.php">
		<input type="text" id="listname" name="list_name" placeholder="List name">
		<input type="text" name="list_description" placeholder="List description">
		<select name="list_privacy" id="listprivacy" class="selectBox">
			<option value="" disabled selected>Select privacy</option>
			<option>Open</option>
			<option>Private</option>
			<option>Unlisted</option>
		</select>
		<select name="list_type" id="listtype" class="selectBox">
			<option value="" disabled selected>Select Type</option>
			<option value="personal">Personal</option>
			<option value="group">Group</option>
		</select>
		<div class="addBookmarkStatus" style="display:none;background-color:rgb(255, 221, 215);font-size: 0.9em; color:rgb(103, 103, 103);padding:5px; margin-bottom:5px">List name or privacy cannot be empty</div>
		<button class="button">Save</button>
	</form>
</div>
<div class="editBookmarkDiv">
	<h4 class="addBookmarkFormHead">Edit List</h4>
	<form class="addBookmarkForm" id="editlistform" method="post" action="dash.php">
		<input type="hidden" name="listid" id="editlistid">
		<input type="text" id="editlistname" name="editlist_name" placeholder="List name">
		<input type="text" id="editdesc" name="editlist_description" placeholder="List description">
		<select name="editlist_privacy" id="editlistprivacy" class="selectBox">
			<option value="" disabled selected>Select privacy</option>
			<option>Open</option>
			<option>Private</option>
			<option>Unlisted</option>
		</select>
		<select name="editlist_type" id="editlisttype" class="selectBox">
			<option value="" disabled selected>Select Type</option>
			<option value="personal">Personal</option>
			<option value="group">Group</option>
		</select>
		<div class="editBookmarkStatus" style="display:none;background-color:#A8A8A8;font-size: 0.9em; color:white;padding:5px; margin-bottom:5px">List name or privacy cannot be empty</div>
		<button class="button">Update</button>
	</form>
</div>
<?php include_once('php_includes/sidebar.php'); ?>

<?php include_once('php_includes/head.php'); ?>

<div class="bodyWrapper">
	<div class="bookmarkOptions" style="height:38px">
		<div class="bookmarkOptionsContent">
			<h5 style="margin-bottom:10px"class="button" id="addBtn"><i class="fa fa-plus" style="margin-right: 5px;"></i> New list</h5>
		</div>
	</div>
	<div class="linkWrapper">
		<?php
			// $listQuery = $db->query('SELECT * FROM list
			// 							WHERE list.list_owner ='.$_SESSION['userid']);
			$allLists = BookmarkList::getFromSql('SELECT * FROM list WHERE list_owner = :listowner',array(':listowner' => $log_id),$db);
		if(count($allLists) > 0 ){
			$hasList = 'true';
			foreach($allLists as $list){
		?>
			<div class="listsWrapper">
				<div style="background-color:#1B2224;float:left; padding:10px; border-right:1px solid #E7DDDD">
					<i class="fa fa-list-ul"></i>
					<?=BookmarkList::getTotalBookmarksFromListId($list->getId(), $db)?>
				</div>
				<div style="float:left;padding: 5px;">
					<span class="iconBtn deleteList" data-id="<?=$list->getId()?>"><i class="iconBg fa fa-trash-o"></i></span>
					<span class="iconBtn editList" data-id="<?=$list->getId()?>"><i class="iconBg fa fa-pencil-square-o"></i></span>
				</div>
				<div style="padding:10px; float:left">
					<div class="listBody">
						<a href="list.php?lid=<?=$list->getId()?>"><span><?=$list->getName()?></span></a>
					</div>
					<div style="clear:both"></div>
				</div>
				<div style="clear:both"></div>

			</div>

		<?php
			}
		}else{
			$hasList = 'false';
		}
		?>
	</div>
</div>
<script>
var isAddFormActive = false;
var isEditFormActive = false;
$('#editlistform').submit(function() {
	if($.trim($('#editlistprivacy').val()) == "" || $.trim($('#editlistname').val()) == ""){
		$('.editBookmarkStatus').slideDown();
		return false;
	}
});
$('#listform').submit(function() {
	if($.trim($('#listprivacy').val()) == "" || $.trim($('#listname').val()) == ""){
		$('.addBookmarkStatus').slideDown();
		return false;
	}
});
$('.deleteList').click(function(){
	//$('').css('display', 'block');
	$('.confirmBox,.overlayBg').css('display','block');
	$('.confirmbtn').attr('data-id', $(this).data('id'));
});

$('.editList').click(function(){
	var listid = $(this).data('id');

	$.ajax({
		url: "php_includes/edit.php",
		data: {lid: listid, type: 'editlist'},
		type: "POST"
	}).done(function(data) {
		//$( this ).addClass( "done" );
		//console.log(data);
		if(data == "bad request"){
			window.location.href = 'error.php';
		}else{
			var jsonArr = JSON.parse(data);
			$('#editlistname').val(jsonArr.listname);
			$('#editdesc').val(jsonArr.listdesc);
			$('#editlistprivacy').val(jsonArr.listprivacy);
			$('#editlisttype').val(jsonArr.listtype);
			$('#editlistid').val(listid);
			//$('.editBookmarkDiv').css('top', '40%');
			$('.editBookmarkDiv').css('display', 'block').center(true);
			isEditFormActive = true;
			$('.overlayBg').css('display', 'block');
		}
	});
});

$('.confirmbtn').click(function(){
	var lid = $(this).data('id');
	var uid = <?=($user_ok)?$log_id:'0'?>;

	$.ajax({
		url: "php_includes/delete.php",
		data: {lid: lid, type: 'list'},
		type: "POST"
	}).done(function(data) {
		//$( this ).addClass( "done" );
		console.log(data);
		if(data == "success"){
			window.location.href = 'dash.php'; 
		}else if(data == "failed"){
			alert('failed');
			window.location.href = 'error.php';
		}
	});
});

$(document).ready(function() {
	var hasList = '<?=$hasList?>';
	if(hasList == 'false'){
		$('.tooltip').css('opacity', '1');
		$('.tooltip').css('top', '88px');
	}else{
		$('.tooltip').css('display', 'none');
	}
});
$('.closetooltip').click(function(){
	$($($(this).parent()).parent()).css('display', 'none');
	if($('.overlayBg').css('display') == 'block'){
		$('.overlayBg').css('display','none');
	}
});
$('.overlayBg').click(function(){
	$('.overlayBg').css('display', 'none');
	$('.addBookmarkDiv,.editBookmarkDiv').css('top', '-500%').css('display','none');
	if($('.confirmBox').css('display') == 'block'){
		$('.confirmBox').css('display','none');
	}
	isAddFormActive = false;
	isEditFormActive = false;
});
$('#addBtn').click(function(){
	$('.overlayBg').css('display', 'block');
	$('.addBookmarkDiv').css('display', 'block').center(true);
	isAddFormActive = true;
});
$(window).scroll(function(){
	if(isAddFormActive){
		$('.addBookmarkDiv').center(true);
	}
	if(isEditFormActive){
		$('.editBookmarkDiv').center(true);
	}
});

jQuery.fn.center = function(parent) {
	if (parent) {
		parent = this.parent();
	} else {
		parent = window;
	}
	this.css({
		"position": "absolute",
		// "top": ((($(parent).height() - this.outerHeight()) / 2) + $(parent).scrollTop() + "px"),
		"top": 100+$(parent).scrollTop()+"px",
		"left": ((($(parent).width() - this.outerWidth()) / 2) + $(parent).scrollLeft() + "px")
	});
return this;
}

</script>
<?php
echo $html->injectFooter(); 

