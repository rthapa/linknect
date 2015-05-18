<?php
session_start();
header('Access-Control-Allow-Origin: *');
require __DIR__.'/config.php';
require __DIR__.'/classes/PDOConnect.php';
require __DIR__.'/classes/BookmarkHelper.php';
require __DIR__.'/classes/Bookmark.php';
require __DIR__.'/classes/BookmarkList.php';
require __DIR__.'/classes/User.php';
//require __DIR__.'/classes/UserGroup.php';
require __DIR__.'/classes/Html.php';

$db = new PDOConnect();


// if(isset($_GET['bid'])){
// 	$bookmark = Bookmark::getFromId($_GET['bid'], $db);
// 	if($bookmark->getId()){
// 		echo $bookmark->getOriginalTitle();
// 		$out = array('status' => $bookmark->getOriginalTitle());
// 	}else{
// 		echo 'no';
// 		$out = array('status' => 'failed');
// 	}
// 	echo json_encode($out);
// }else{
// 	$out = array('status' => 'failed');
// 	echo json_encode($out);
// }

$html = new Html();
$html->title = 'Linknect';
$html->faviconUrl = 'favicon.ico';
$html->css[] = '<link rel="stylesheet" type="text/css" href="dashstyles.css">';
$html->meta[] = '<meta name="Description" content="Linknect is an online bookmark management system which allows you to store, share and access your bookmarks online.">';
$html->meta[] = '<meta name="Keywords" content="online, bookmark, manager, bookie, link, href, save, cross platform, share, list">';
echo $html->injectHeader();
?>
<div>
	<div id="test">
	</div>

	<div id="bodyContent">
	</div>
</div>
<script>
	//# hash and salt the password in cookie
	
	//if isset cookie = username and password
	//send username and password via ajax
	//check if user exist
	//if exist send the requested data
	
	//if cookie = username and password is not set
	//show login div
	//send login inputs via ajax
	//if user exist send found status
	//set the cookie username and password for 30 days 

	$(function(){
		if($.trim($.cookie('user')) != '' && $.trim($.cookie('password')) != ''){
			console.log('im here');
			$.ajax({
				url: 'http://beta.linknect.com/api.php',
				data: {type: 'getList', user: $.cookie('user'), password: $.cookie('password')},
				type: 'GET',
				dataType: 'json',
				success: function(data){
					console.log(data);
					$.each(data, function(i, v){
						$('#bodyContent').append('<h5>'+v.name+'</h5>');
					});
				},
				error: function(){
					console.log('error');
				}
			});
		}else{
			var form = $('<form id="loginForm"></form>');
			var userInput = $('<input type="text" name="username" id="username" />');
			var passwordInput = $('<input type="password" name="password" id="password" />');
			var submitBtn = $('<button type="submit">submit</button>');

			$.each([userInput, passwordInput, submitBtn], function(i, v){
				form.append(v);
			});

			$('#bodyContent').append(form);
		}
	});

	$('body').on('submit', '#loginForm',function(){
		alert('submit');
		return false;
	});

	// $.ajax({
	// 	url: 'http://beta.linknect.com/api.php',
	// 	data: {bid: '52'},
	// 	type: 'GET',
	// 	dataType: 'json',
	// 	success: function(data){
	// 		console.log(data);
	// 		//$('#test').html(data.status1);
	// 	},
	// 	error: function(){
	// 		console.log('error');
	// 	}
	// });

	// if($.trim($.cookie('jqueryCookieTest')) != ''){
	// 	console.log($.cookie('jqueryCookieTest'));
	// }
	//$.cookie('jqueryCookieTest', 'test', { expires: 7 });
	$.cookie('user', 'rthapa90@gmail.com', { expires: 7 });
	$.cookie('password', '123', { expires: 7 });
</script>
<?php
//setcookie("testApi", 'im api and set', strtotime( '+30 days' ), "/", "", "", TRUE);
// if(isset($_COOKIE["jqueryCookieTest"])){
// 	echo 'set';
// }else{
// 	echo  'not set';
// }
//setcookie("jqueryCookieTest", '', strtotime( '-5 days' ), '/');
