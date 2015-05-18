<div class="sidebar">
	<div class="userinfo">
		<div class="userAvatar">
			<img src="images/avatar.png">
		</div>
		<h4>
			<?=$user->getUsername();?>
			<span class="settingDropDownIcon">
				<i class="fa fa-chevron-down"></i>
			</span>
			
		</h4>
		<!-- <a href="logout.php"><button class="btn" style="margin-left:43px;"><i class="fa fa-power-off"></i> Log out</button></a> -->
	</div>
	<div class="userCtrlWrapper">
		<h5 class="sidebarHead"><i class="sidebarIcon fa fa-cog"></i> SETTING</h5>
		<div class="userCtrl">
			<ul>
				<a href="edit.php?uid=<?=$user->getId()?>"><li>Edit<span class="bookmarksCount"><i class="fa fa-pencil"></i></span></li></a>
				<a href="logout.php"><li>Log out<span class="bookmarksCount"><i class="fa fa-power-off"></i></span></li></a>
			</ul>
		</div>
	</div>
	<a href="dash.php"><h5 class="sidebarHead"><i class="sidebarIcon fa fa-book"></i> LISTS</h5></a>
	<div class="userList "> <!--nano-->
		<ul class="nano-content">
		<?php
			$list = BookmarkList::getFromSql('SELECT * FROM list WHERE list_owner = :listowner',array(':listowner' => $log_id),$db);
			// $limitExceed = false;
			// if(count($list)>6){
			// 	$list = BookmarkList::getFromSql('SELECT * FROM list WHERE list_owner = :listowner LIMIT 6',array(':listowner' => $log_id),$db);
			// 	$limitExceed = true;
			// }
			foreach($list as $l){
		?>
			<a href="list.php?lid=<?=$l->getId()?>">
				<li>
					<span class="truncate"><?=$l->getName()?></span>
					<span class="bookmarksCount">
						<i class="fa fa-paperclip"></i> <?=BookmarkList::getTotalBookmarksFromListId($l->getId(), $db)?>
					</span>
					<div style="clear:both;"></div>
				</li>
			</a>
		<?php
			}
		?>
		<?php //if($limitExceed): ?>
			<!-- <li><a href="dash.php">more .. </a></li> -->
		<?php //endif; ?>
		</ul>
	</div>
	<a href="dash.php"><h5 class="sidebarHead"><i class="sidebarIcon fa fa-star"></i> WATCHING</h5></a>
</div>
<script>
// $(".nano").nanoScroller();
</script>
