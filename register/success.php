<?
	$username = $pathOptions[1];
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Thank you for registering, <?=$username?>!</h1>
		<p>An email has been sent to you with instructions on how to activate your account.</p>
		<p>If you do not recieve an email in your inbox, please check your spam folder.</p>
<? require_once(FILEROOT.'/footer.php'); ?>