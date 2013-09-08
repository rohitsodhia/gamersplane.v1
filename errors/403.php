<?
	header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
	$loggedIn = checkLogin(0);
	
	$errorPage = TRUE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
			<h1>403 Error</h1>
			<p>Don't peak behind the DM screen!</p>
			<p>You might want to try looking somewhere else.</p>
<? require_once(FILEROOT.'/footer.php'); ?>