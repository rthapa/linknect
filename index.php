<?php
include_once("check_login_status.php");

//include_once("/classes/Html.php");
if($user_ok){
	header("location: dash.php");
	exit();
}
//BookmarkHelper::updateReport('1', $db);

$html = new Html();
$html->title = 'Linknect';
$html->faviconUrl = 'favicon.ico';
$html->css[] = '<link rel="stylesheet" type="text/css" href="css/linknect.css">';
$html->meta[] = '<meta name="Description" content="Linknect is an online bookmark management system which allows you to store, share and access your bookmarks online.">';
$html->meta[] = '<meta name="Keywords" content="online, bookmark, manager, bookie, link, href, save, cross platform, share, list">';
echo $html->injectHeader();
?>
<!-- <nav>
</nav> -->
<div class="formWrapper">
	<div class="loginWrapper">
		<div class="rightDiv">
			<div class="loginLogo">
				<span><img alt="linknect title" src="images/linknect.png"></span>
			</div>
			<div><img alt="linknect description" src="images/desc.png"></div>
			<form id="loginform">
				<span class="inputWrapper">
					<span><i class="fa fa-user"></i></span>
					<input class="formIn" id="email" type="text" id="email"  maxlength="88" placeholder="Email">
				</span>
				<br/>
				<div style="clear:both"></div>
				<span class="inputWrapper">
					<span><i class="fa fa-lock"></i></span>
					<input class="formIn" type="password" id="password"  maxlength="100" placeholder="Password"> 
				</span>
				<div style="clear:both"></div>
				<p id="status"></p>
				<span>
					 <button style="width: 250px;padding: 10px; margin-top: 5px;" class="button" type="submit" id="loginbtn">
					 	<span>Log in</span>
					 </button>
				</span>
			</form>
			<span class="loginOption">
				<a href="signup.php" style="margin-right: 19px;">Create Account</a>
				<a href="#">Forgot Password</a>
			</span>
		</div>
		<div class="leftDiv">
			<p style="font-size:1.1em; line-height:23px">Linknect is an online bookmark management system which allows you to store, share and access your bookmarks online. </p>
		</div>
		<div style="clear:both"></div>
	</div>
</div>
<script>
$('#loginform').on('submit',function(){
	if($.trim($('#email').val()) == '' || $.trim($('#password').val()) == '' ){
		$('#status')
		.html("<i class='fa fa-exclamation'></i> Please fill all the forms")
		.css('display', 'block');
		return false;
	}else{
		var email = $('#email').val();
		var password = $('#password').val();
		$.ajax({
			url: "ajax/noauthajax.php",
			data: {email: email, password: password, type: 'login'},
			type: "POST",
			dataType: "json",
			success: function(data){
				if(data.status == 'success'){
					window.location.href = 'dash.php';
				}else if(data.status == 'failed'){
					//failed login
					$('#status').css('display', 'block').html('The email/username or password is incorrect.');
				}
			},
			error: function(){
				//bad request
			}
		});
	}
	return false;
});
</script>
<?php
// echo $html->injectFooter();
