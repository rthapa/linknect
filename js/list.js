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

var list = {
	'isAddFormActive': false,
	'isEditFormActive' : false,
	init : function(){
		list.loadEventHandler();
	},

	loadEventHandler : function(){
		$('.closetooltip').click(function(){
			$($($(this).parent()).parent()).css('display', 'none');
			if($('.overlayBg').css('display') == 'block'){
				$('.overlayBg').css('display','none');
			}
		});

		$('.editBookmark').click(function(){
			var bookmarkid = $(this).data('id');

			$.ajax({
				url: "php_includes/edit.php",
				data: {bookmarkid: bookmarkid, type: 'editBookmark'},
				type: "POST"
			}).done(function(data) {
				//$( this ).addClass( "done" );
				console.log(data);
				if(data == "bad request"){
					window.location.href = 'error.php';
				}else{
					var jsonArr = JSON.parse(data);
					$('#editbookmarklink').val(jsonArr.blink);
					$('#editbookmarkdesc').val(jsonArr.bdesc);
					$('#editbookmarkid').val(bookmarkid);
				}
			});

			$('.editBookmarkDiv').css('display', 'block').center(true);
			isEditFormActive = true;
			$('.overlayBg').css('display', 'block');
		});

		$('#bookmarkform').submit(function() {
			if($.trim($('#bookmarklink').val()) == ""){
				$('#addBookmarkStatus').slideDown();
				return false;
			}

			// $.ajax({
			// 	url: 'ajax/bookmark.php',
			// 	type: 'POST',
			// 	data: {type: 'saveBookmark', link: $('#bookmarklink').val(), desc: $('#bookmarkdesc').val()},
			// 	dataType: 'json',
			// 	success: function(data){
			// 		console.log(data);
			// 	}
			// });

			// return false;
		});

		$('#shareList').click(function(){
			if($('.shareLinkDiv').css('display') == 'none'){
				$('.shareLinkDiv').slideDown();
			}else{
				$('.shareLinkDiv').slideUp();
			}
			
		});

		$('.overlayBg').click(function(){
			$('.overlayBg').css('display', 'none');
			$('.addBookmarkDiv').css('top', '-500%').css('display','none');
			$('.editBookmarkDiv').css('top', '-500%').css('display','none');
			$('.addUserToGroupDiv').css('top', '-500%').css('display','none');
			list.isAddFormActive = false;
			list.isEditFormActive = false;
		});

		$('#addBtn').click(function(){
			if($('.tooltip').css('display') == 'block') $('.tooltip').css('display', 'none');
			$('.overlayBg').css('display', 'block');
			$('.addBookmarkDiv').css('display', 'block').center(true);
			list.isAddFormActive = true;
		});

		$('#addUserBtn').click(function(){
			if($('.tooltip').css('display') == 'block') $('.tooltip').css('display', 'none');
			$('.overlayBg').css('display', 'block');
			$('.addUserToGroupDiv').css('display', 'block').center(true);
			list.isAddFormActive = true;
		});

		$(window).scroll(function(){
			if(list.isAddFormActive){
				$('.addBookmarkDiv').center(true);
			}

			if(list.isEditFormActive){
				$('.editBookmarkDiv').center(true);
			}
		});

		$('body').on('mouseover', '.gridRight', function(){
			$('.gridEditIcon').css('margin-top', '142px');
		});

		$('body').on('mouseout', '.gridRight', function(){
			$('.gridEditIcon').css('margin-top', '220px');
		});
	}
}

var sidebar = {
	init : function(){
		sidebar.loadEventHandler();
	},

	loadEventHandler : function(){
		$('.userinfo h4').on('click', function(){
			if($('.userCtrlWrapper').css('display') != 'block'){
				$('.userCtrlWrapper').slideDown();
			}else{
				$('.userCtrlWrapper').slideUp();
			}
		});

		$('.notificationBtn').on('click',function(){
			if($('.notificationDiv').css('display') == 'block'){
				$('.notificationDiv').css('display', 'none');
			}else{
				$('.notificationDiv').css('display', 'block');
			}
		});
	}
}
