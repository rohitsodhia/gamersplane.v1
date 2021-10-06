<?
	$responsivePage=true;
	header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');

	$errorPage = TRUE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
			<h1 class="headerbar"><i class="ra ra-telescope"></i> 404 Error</h1>
			<p>Your treasure is in another dungeon!</p>
			<p>You might want to try looking somewhere else.</p>
<? require_once(FILEROOT.'/footer.php'); ?>