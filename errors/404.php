<?
	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
	$loggedIn = checkLogin(0);
	
	$errorPage = TRUE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
			<h1>404 Error</h1>
			<p>Your treasure is in another dungeon!</p>
			<p>You might want to try looking somewhere else.</p>
<? require_once(FILEROOT.'/footer.php'); ?>