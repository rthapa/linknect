<?php
include_once("check_login_status.php");

if($user_ok == true){
	header("location: dash.php");
	exit();
}

// if(isset($_POST['email'])){
// 	var_dump($_POST);
// 	exit;
// }

// if(isset($_POST['checkmail'])){
// 	$query = $db->query("SELECT * FROM users WHERE user_email = :email",
// 						array(":email"=>$_POST['checkmail']));
// 	 if(count($query) == 1){
// 	 	echo trim('found');
// 	 	exit;
// 	 }else{
// 	 	echo 'not found';
// 	 	exit;
// 	 }
// }

$html = new Html();
$html->title = 'Sign up';
$html->faviconUrl = 'favicon.ico';
$html->css[] = '<link rel="stylesheet" type="text/css" href="dashstyles.css">';
$html->meta[] = '<meta name="Description" content="Linknect is a online bookmark management system where you can store, share and access your bookmarks no matter where you are.">';
$html->meta[] = '<meta name="Keywords" content="signup, register, bookmark, manager, online, create, account">';
echo $html->injectHeader();
?>
<!-- <!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="Description" content="Linknect is a online bookmark management system where you can store, share and access your bookmarks no matter where you are.">
	<meta name="Keywords" content="signup, register, bookmark, manager, online, create, account">
	<link rel="stylesheet" type="text/css" href="dashstyles.css">
	<link rel="shortcut icon" href="favicon.ico">
	<script src="js/main.js"  type="text/javascript"></script>
	<script src="js/ajax.js"  type="text/javascript"></script>
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
	<script  type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script  type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script src="inteljs.js" type="text/javascript"></script>
</head> -->

<!-- <body class="indexWrapper" style="background-color:#F0F0F0"> -->
	<div class="formWrapper">
		<div class="loginWrapper">
			<!-- <span class="inputWrapper">
				<button class="loginFb"><img src="images/fb.png"> Login with Facebook</button>
			</span> -->
			<!-- <div class="white-separator">
				<hr class="white-separator-bevel">
			</div> -->
			<span class="signuptooltip">username already taken</span>
			<div class="loginLogo">
				<span><img alt="linknect title" src="images/linknect.png"></span>
			</div>
			<div><img alt="linknect description" src="images/desc.png"></div>
			<form style="padding:20px" id="signupForm"><!-- action="signup.php" method="post"-->
				<span class="inputWrapper">
					<span><i class="fa fa-user"></i></span>
					<input class="formIn" id="username" name="username" type="text" maxlength="88" placeholder="Username">
				</span>
				<br/>
				<span class="inputWrapper">
					<span><i class="fa fa-user"></i></span>
					<input class="formIn" id="email" name="email" type="text" maxlength="88" placeholder="Email">
					<i id="usernameavailable" style="right: 16px; display:none;" class="fa fa-check-circle availableicon"></i>
					<i id="usernametaken" style="right: 16px; display:none;" class="fa fa-times-circle takenicon"></i>
				</span>
				<br/>
				<div style="clear:both"></div>
				<span class="inputWrapper">
					<span><i class="fa fa-lock"></i></span>
					<input class="formIn" type="password" name="password" id="password" maxlength="100" placeholder="Password"> 
					<i id="emailavailable" style="right: 11px; display:none;" class="fa fa-check-circle availableicon"></i>
					<i id="emailtaken" style="right: 11px; display:none;" class="fa fa-times-circle takenicon"></i>
				</span>
				<div style="clear:both"></div>
				<span class="inputWrapper">
					<span><i class="fa fa-lock"></i></span>
					<input class="formIn" type="password" id="repassword" name="repassword" maxlength="100" placeholder="Confirm Password"> 
				</span>
				<div style="clear:both"></div>
				<p id="status"></p>
				<span>
					 <button style="width: 250px;margin-top: 10px;padding: 10px;" class="button" type="submit">
					 	<span>Sign up</span>
					 </button>
				</span>
			</form> 
			<span class="loginOption" style="margin-left:126px;">
				<a href="index.php" style="margin-right: 19px;color: #fff;">Log in</a>
			</span>
			<p style="color: #fff; text-align:center;font-size: 13px;">By creating this account you accept the terms and conditions of bookmarks.com</p>
		</div>
	</div>
	<script type="text/javascript">
		$('#username').blur(function() {
			if($.trim($(this).val()) != '' && $.trim($(this).val()).length > 3){
				$.ajax({
					url: "php_includes/registerlogic.php",
					type: "POST",
					data: {checkusername: $('#username').val()},
					dataType: 'json',
				}).done(function(data){
					var resultObject = $.parseJSON(JSON.stringify(data));
					console.log(resultObject.data);
					 if(resultObject.data=='taken'){
					 	$('#usernametaken').css('display','block');
					 	$('#usernametaken').attr('data-info', '004');
					 }else{
					 	$('#usernameavailable').css('display','block');
					 	$('#usernameavailable').attr('data-info', '005');
					 }
				});
			}
		});

		$('#email').blur(function() {
			if($.trim($(this).val()) != ''){
				$.ajax({
					url: "php_includes/registerlogic.php",
					type: "POST",
					data: {checkemail: $('#email').val()},
					dataType: 'json',
				}).done(function(data){
					var resultObject = $.parseJSON(JSON.stringify(data));
					console.log(resultObject.data);
					 if(resultObject.data=='taken'){
					 	$('#emailtaken').css('display','block');
					 	$('#emailtaken').attr('data-info', '001');
					 }else{
					 	if(!validateEmail($.trim($('#email').val()))){
					 		console.log('validate email');
							$('#emailtaken').css('display','block');
							$('#emailtaken').attr('data-info', '002');
						}else{
							$('#emailavailable').css('display','block');
							$('#emailavailable').attr('data-info', '003');
						}
					 	
					 }
				});
			}
		});

		var msg = "";
		$('.takenicon ,.availableicon').mouseenter(function(){
			var infoType = $(this).data('info');
			console.log(infoType);
			
			switch(infoType){
				case "001":
					msg = '<i class="fa fa-exclamation"></i> Invalid email format or email already registered';
					break;

				case "002":
					msg = '<i class="fa fa-exclamation"></i> Invalid email format or email already registered';
					break;

				case "003":
					msg =  '<i class="fa fa-exclamation"></i> Email available';
					break;
				case "004":
					msg =  '<i class="fa fa-exclamation"></i> Username taken';
					break;
				case "005":
					msg =  '<i class="fa fa-exclamation"></i> Username available';
					break;
			}

			$('.signuptooltip').html(msg);
			var offset = $(this).offset();
			if(infoType == '001' || infoType == '002' ){
				$('.signuptooltip').css('top', offset.top-14).css('left', offset.left+16).css('width', 'inherit');
				$('.signuptooltip').css('display','block');
			}else{
				$('.signuptooltip').css('top', offset.top-14).css('left', offset.left+16).css('width', 'auto');;
				$('.signuptooltip').css('display','block');
			}
		});

		$('.takenicon ,.availableicon').mouseout(function(){
			$('.signuptooltip').css('display','none');
		});


		$('#email').focus(function(){
			$('#emailtaken').css('display','none');
			$('#emailavailable').css('display','none');
			$('.signuptooltip').css('display','none');
		});

		$('#username').focus(function(){
			$('#usernametaken').css('display','none');
			$('#usernameavailable').css('display','none');
			$('.signuptooltip').css('display','none');
		});


		$('#signupForm').submit(function(){
			if($.trim($('#email').val()) == '' ||
			$.trim($('#username').val()) == '' ||
			$.trim($('#password').val()) == '' ||
			$.trim($('#repassword').val()) == ''){
				//alert('fill form');
				
				$('#status').html('<i class="fa fa-exclamation"></i> Please fill all the forms').css('display','block');
				return false;
			}

			if(!validateEmail($.trim($('#email').val()))){
				$('#status').html('<i class="fa fa-exclamation"></i> Invalid email format').css('display','block');
				return false;
			}

			if($.trim($('#password').val()) != $.trim($('#repassword').val())){
				$('#status').html('<i class="fa fa-exclamation"></i> Password does not match').css('display','block');
				return false;
			}

			$(this).attr('action', "php_includes/registerlogic.php");
			$(this).attr('method', "POST");
		});

		function validateEmail(email) { 
			var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test(email);
		} 

	</script>
<?php
echo $html->injectFooter(); 
