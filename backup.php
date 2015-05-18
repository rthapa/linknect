<div class="linkWrapper">
		<?php
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
				<div class="bookmarkWrapper">
					<div class="bookmarkHeader">
						<a href="<?=$bookmark->getLink()?>" target="_blank">
						<!--icon class create-->
						<span class="headerIcon">
						<img height="16" width="16" src="<?=(trim($bookmark->getIcon()) != ''?$bookmark->getIcon() : \BookmarkHelper::getIcon($bookmark->getLink()))?>">
						</span>
						<div style="float:left; padding:10px" class="bookmarkHeaderTitle"><h4 style="overflow: hidden;max-width:550px;text-overflow: ellipsis;"><?=$bookmark->getOriginalTitle()?></h4></div>
						</a>
						<?php if($user_ok && $isOwnerTheViewer):?>
							<div class="bookmarkHeaderRightOptions">
								<span class="iconBtn deleteBookmark" data-id="<?=$bookmark->getId()?>"><i class="iconBg fa fa-trash-o"></i></span>
								<span class="iconBtn editBookmark"  data-id="<?=$bookmark->getId()?>"><i class="iconBg fa fa-pencil-square-o"></i></span>
							</div>
						<?php endif;?>
						<div class="bookmarkHeaderRight">
							<span><i class="fa fa-calendar"></i> <?=date("d M y",strtotime($bookmark->getDate()))?></span>
						</div>
						<div style="clear:both"></div>
					</div>
					<div class="bookmarkBody">
						<a href="<?=$bookmark->getLink()?>" target="_blank"><p class="wrapText"><i class="fa fa-paperclip"></i> <?=$bookmark->getLink()?></p></a>
						<?php if(trim($bookmark->getDescription()) != ""){ ?>
							<p class="bookmarknote"><?=$bookmark->getDescription()?></p>
						<?php } ?>
					</div>
				</div>
				<?php
			}
		?>
	</div>


<!-- ALTER TABLE `bookmarks`
ADD COLUMN `bookmark_source`  varchar(255) NULL AFTER `bookmark_date`;

ALTER TABLE `bookmarks`
ADD COLUMN `bookmark_thumbnail`  varchar(255) NOT NULL AFTER `bookmark_source`; -->


<!-- ALTER TABLE `list`
ADD COLUMN `list_color`  varchar(255) NULL AFTER `list_datetime`,
ADD COLUMN `list_type`  varchar(255) NULL AFTER `list_color`; -->

