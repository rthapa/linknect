<?php
$message= 'Oops the page you are looking could not be found';
if(isset($_GET['type'])){
	if($_GET['type'] == '104'){
		$message = 'Something went wrong. Make sure you have Javascript turned on and please try again.';
	}
}
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<div>
		<p><?=$message?></p>
	</div>
</body>
</html>
