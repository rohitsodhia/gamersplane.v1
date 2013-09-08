<?
	$loggedIn = checkLogin(0);
	
	$username = $pathOptions[1];
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Thank you for registering, <?=$username?>!</h1>
		<p>An email has been sent to you with instructions on how to activiate your account.</p>
		<p>If you do not recieve an email in your inbox, please check your spam folder.</p>
<? require_once(FILEROOT.'/footer.php'); ?>